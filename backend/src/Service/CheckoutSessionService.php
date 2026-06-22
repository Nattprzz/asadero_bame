<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\LocalStatus;
use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Enum\ProductAvailability;
use App\Repository\LocalRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CheckoutSessionService
{
    private const CHECKOUT_CURRENCY = 'eur';

    private ?StripeClient $stripe = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LocalRepository $locals,
        private readonly ProductRepository $products,
        private readonly OrderRepository $orders,
        private readonly InputValidator $validator,
        private readonly OrderStockService $stockService,
        private readonly StripeConfigValidator $configValidator,
        private readonly CheckoutOriginValidator $originValidator,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(STRIPE_SECRET_KEY)%')]
        private readonly string $stripeSecretKey,
    ) {}

    /**
     * @return array{order: CustomerOrder, paymentMethod: string, paymentStatus: string, requiresOnlinePayment: bool, checkoutUrl?: string}
     */
    public function createCheckoutSession(User $user, array $data, ?string $providedIdempotencyKey = null): array
    {
        $paymentMethod = $this->resolvePaymentMethod($data);
        $local = $this->getLocal($data['localId'] ?? $data['local_id'] ?? null);
        $items = $data['items'] ?? [];

        if (!is_array($items) || $items === []) {
            throw new BadRequestHttpException('El pago debe incluir al menos un producto.');
        }

        $items = $this->groupCheckoutItems($items);

        $type = (string) ($data['type'] ?? OrderType::TAKEAWAY);
        $this->validator->orderType($type);

        $order = (new CustomerOrder())
            ->setReference($this->generateReference())
            ->setUser($user)
            ->setLocal($local)
            ->setType($type)
            ->setStatus(PaymentMethod::requiresOnlinePayment($paymentMethod) ? OrderStatus::PENDING : OrderStatus::CONFIRMED)
            ->setPaymentMethod($paymentMethod)
            ->setPaymentStatus(PaymentStatus::PENDING)
            ->setNotes($data['notes'] ?? $data['notas'] ?? null)
            ->setPhone($data['phone'] ?? $data['telefono'] ?? $user->getPhone())
            ->setAddress($data['address'] ?? $data['direccion'] ?? null);

        $lineItems = [];
        $total = 0.0;

        foreach ($items as $rawItem) {
            if (!is_array($rawItem)) {
                throw new BadRequestHttpException('Producto de pago no valido.');
            }

            $product = $this->getProduct($rawItem['productId'] ?? $rawItem['product_id'] ?? $rawItem['id'] ?? null);
            $quantity = (int) ($rawItem['quantity'] ?? $rawItem['cantidad'] ?? 0);

            if ($quantity < 1) {
                throw new BadRequestHttpException('La cantidad minima es 1.');
            }

            $unitAmount = (int) round(((float) $product->getPrice()) * 100);

            $lineItems[] = [
                'quantity' => $quantity,
                'price_data' => [
                    'currency' => self::CHECKOUT_CURRENCY,
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                ],
            ];

            $order->addLine(
                (new OrderLine())
                    ->setProduct($product)
                    ->setQuantity($quantity)
                    ->setUnitPrice($product->getPrice())
                    ->setNotes($rawItem['notes'] ?? null)
            );

            $total += ((float) $product->getPrice()) * $quantity;
        }

        $order->setTotal($total);

        if (!PaymentMethod::requiresOnlinePayment($paymentMethod)) {
            return $this->createPayAtStoreOrder($local, $order, $paymentMethod);
        }

        $idempotencyKey = $this->checkoutIdempotencyKey(
            $user,
            $local,
            $order,
            $providedIdempotencyKey
        );
        $existingOrder = $this->orders->findCheckoutByIdempotencyKey($idempotencyKey);

        if ($existingOrder instanceof CustomerOrder) {
            $this->logger->info('Stripe checkout reused from idempotency key.', [
                'order_id' => $existingOrder->getId(),
                'user_id' => $user->getId(),
            ]);

            return $this->existingCheckoutResult($existingOrder);
        }

        $order->setCheckoutIdempotencyKey($idempotencyKey);

        $this->configValidator->assertSecretKey($this->stripeSecretKey);

        $successUrl = $this->originValidator->assertReturnUrl((string) ($data['successUrl'] ?? $data['success_url'] ?? ''));
        $cancelUrl = $this->originValidator->assertReturnUrl((string) ($data['cancelUrl'] ?? $data['cancel_url'] ?? ''));

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->stockService->reserve($local, $order);
            $this->entityManager->flush();

            $session = $this->stripe()->checkout->sessions->create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'checkout_idempotency_key' => $idempotencyKey,
                ],
            ], ['idempotency_key' => $idempotencyKey]);

            if (!is_string($session->id) || $session->id === '' || !is_string($session->url) || $session->url === '') {
                throw new \RuntimeException('Stripe no devolvio una sesion de Checkout valida.');
            }

            $this->stripe()->checkout->sessions->update($session->id, [
                'metadata' => [
                    'checkout_idempotency_key' => $idempotencyKey,
                    'order_id' => (string) $order->getId(),
                ],
            ], ['idempotency_key' => $idempotencyKey.'-order-'.$order->getId()]);

            $order
                ->setStripeCheckoutSessionId($session->id)
                ->setStripeCheckoutUrl($session->url);

            $this->entityManager->flush();
            $connection->commit();

            $this->logger->info('Stripe checkout session created.', [
                'order_id' => $order->getId(),
                'user_id' => $user->getId(),
                'local_id' => $local->getId(),
                'payment_method' => $paymentMethod,
            ]);
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            $this->entityManager->clear();

            if ($exception instanceof UniqueConstraintViolationException) {
                $existingOrder = $this->orders->findCheckoutByIdempotencyKey($idempotencyKey);

                if ($existingOrder instanceof CustomerOrder) {
                    return $this->existingCheckoutResult($existingOrder);
                }
            }

            throw $exception;
        }

        return [
            'order' => $order,
            'paymentMethod' => $paymentMethod,
            'paymentStatus' => $order->getPaymentStatus(),
            'requiresOnlinePayment' => true,
            'checkoutUrl' => $order->getStripeCheckoutUrl(),
        ];
    }

    /**
     * @return array{order: CustomerOrder, paymentMethod: string, paymentStatus: string, requiresOnlinePayment: true, checkoutUrl: string}
     */
    private function existingCheckoutResult(CustomerOrder $order): array
    {
        $sessionId = $order->getStripeCheckoutSessionId();
        $checkoutUrl = $order->getStripeCheckoutUrl();

        if ($sessionId === null || $sessionId === '' || $checkoutUrl === null || $checkoutUrl === '') {
            throw new \RuntimeException('El Checkout previo esta pendiente de reconciliacion con Stripe.');
        }

        return [
            'order' => $order,
            'paymentMethod' => $order->getPaymentMethod(),
            'paymentStatus' => $order->getPaymentStatus(),
            'requiresOnlinePayment' => true,
            'checkoutUrl' => $checkoutUrl,
        ];
    }

    private function resolvePaymentMethod(array $data): string
    {
        $rawMethod = $data['paymentMethod'] ?? $data['payment_method'] ?? null;

        if (!is_string($rawMethod) || trim($rawMethod) === '') {
            throw new BadRequestHttpException(json_encode([
                'paymentMethod' => ['Este campo es obligatorio.'],
            ], JSON_THROW_ON_ERROR));
        }

        $paymentMethod = PaymentMethod::normalize($rawMethod);
        $this->validator->paymentMethod($paymentMethod);

        return $paymentMethod;
    }

    /**
     * @param array<int, mixed> $items
     * @return array<int, array{productId: int, quantity: int, notes: mixed}>
     */
    private function groupCheckoutItems(array $items): array
    {
        $grouped = [];

        foreach ($items as $rawItem) {
            if (!is_array($rawItem)) {
                throw new BadRequestHttpException('Producto de pago no valido.');
            }

            $product = $this->getProduct($rawItem['productId'] ?? $rawItem['product_id'] ?? $rawItem['id'] ?? null);
            $productId = $product->getId();
            $quantity = $this->positiveQuantity($rawItem['quantity'] ?? $rawItem['cantidad'] ?? null);

            if ($productId === null) {
                throw new BadRequestHttpException('Producto de pago no valido.');
            }

            if (!isset($grouped[$productId])) {
                $grouped[$productId] = [
                    'productId' => $productId,
                    'quantity' => 0,
                    'notes' => $rawItem['notes'] ?? null,
                ];
            }

            if ($quantity > PHP_INT_MAX - $grouped[$productId]['quantity']) {
                throw new UnprocessableEntityHttpException('Cantidad de producto fuera de rango.');
            }

            $grouped[$productId]['quantity'] += $quantity;
        }

        ksort($grouped, SORT_NUMERIC);

        return array_values($grouped);
    }

    private function positiveQuantity(mixed $quantity): int
    {
        if (!is_int($quantity) && !is_string($quantity)) {
            throw new UnprocessableEntityHttpException('La cantidad debe ser un entero positivo.');
        }

        $validated = filter_var($quantity, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($validated === false) {
            throw new UnprocessableEntityHttpException('La cantidad debe ser un entero positivo.');
        }

        return $validated;
    }

    /**
     * @return array{order: CustomerOrder, paymentMethod: string, paymentStatus: string, requiresOnlinePayment: bool}
     */
    private function createPayAtStoreOrder(Local $local, CustomerOrder $order, string $paymentMethod): array
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->entityManager->persist($order);
            $this->stockService->reserve($local, $order);
            $this->entityManager->flush();
            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            $this->entityManager->clear();
            throw $exception;
        }

        return [
            'order' => $order,
            'paymentMethod' => $paymentMethod,
            'paymentStatus' => $order->getPaymentStatus(),
            'requiresOnlinePayment' => false,
        ];
    }

    private function getLocal(mixed $localId): Local
    {
        if ($localId === null || (int) $localId < 1) {
            throw new BadRequestHttpException('localId es obligatorio.');
        }

        $local = $this->locals->find((int) $localId);

        if (
            !$local instanceof Local
            || !$local->isActive()
            || !in_array($local->getStatus(), [LocalStatus::OPEN, LocalStatus::CLOSE_SOON], true)
        ) {
            throw new BadRequestHttpException('Local no disponible.');
        }

        return $local;
    }

    private function getProduct(mixed $productId): Product
    {
        if ($productId === null || (int) $productId < 1) {
            throw new BadRequestHttpException('productId es obligatorio.');
        }

        $product = $this->products->find((int) $productId);

        if (
            !$product instanceof Product
            || !$product->isAvailable()
            || in_array($product->getAvailability(), [ProductAvailability::HIDDEN, ProductAvailability::SOLD_OUT], true)
        ) {
            throw new BadRequestHttpException('Producto no disponible.');
        }

        return $product;
    }

    private function checkoutIdempotencyKey(
        User $user,
        Local $local,
        CustomerOrder $order,
        ?string $providedKey
    ): string {
        $providedKey = trim((string) $providedKey);

        if ($providedKey !== '') {
            return hash('sha256', sprintf('client|%s|%s', $user->getId(), $providedKey));
        }

        $lines = [];

        foreach ($order->getLines() as $line) {
            $lines[] = [
                'productId' => $line->getProduct()?->getId(),
                'quantity' => $line->getQuantity(),
                'unitPrice' => $line->getUnitPrice(),
            ];
        }

        $fingerprint = json_encode([
            'userId' => $user->getId(),
            'localId' => $local->getId(),
            'type' => $order->getType(),
            'paymentMethod' => $order->getPaymentMethod(),
            'total' => $order->getTotal(),
            'lines' => $lines,
            'window' => intdiv(time(), 300),
        ], JSON_THROW_ON_ERROR);

        return hash('sha256', 'generated|'.$fingerprint);
    }

    private function stripe(): StripeClient
    {
        if ($this->stripe === null) {
            $this->stripe = new StripeClient($this->stripeSecretKey);
        }

        return $this->stripe;
    }
}
