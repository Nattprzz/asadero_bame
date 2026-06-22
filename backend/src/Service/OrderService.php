<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderService.php — gestión de pedidos.
//
// Este servicio centraliza la creación, consulta visible, cancelación y cambio
// de estado de pedidos. También calcula el total, valida los productos y genera
// una referencia única para cada pedido.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Enum\ProductAvailability;
use App\Repository\LocalRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orders,
        private readonly LocalRepository $locals,
        private readonly ProductRepository $products,
        private readonly InputValidator $validator,
        private readonly OrderStockService $stockService,
        private readonly OrderAuthorizationService $authorization,
        private readonly OrderCancellationService $cancellation,
        private readonly LoggerInterface $logger,
    ) {}

    // Crea un nuevo pedido con sus líneas y calcula el total.
    public function create(User $user, array $data): CustomerOrder
    {
        try {
            $this->validator->requireFields($data, ['type', 'lines']);
            $this->validator->orderType((string) $data['type']);
            $paymentMethod = PaymentMethod::normalize((string) ($data['paymentMethod'] ?? $data['payment_method'] ?? PaymentMethod::PAY_AT_STORE));
            $this->validator->paymentMethod($paymentMethod);
        } catch (BadRequestHttpException $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage(), $exception);
        }

        if (!is_array($data['lines']) || $data['lines'] === []) {
            throw new UnprocessableEntityHttpException('El pedido debe tener al menos una linea.');
        }

        $localId = $this->positiveInteger($data['localId'] ?? $data['local_id'] ?? null, 'localId es obligatorio.');
        $local = $this->locals->find($localId);

        if ($local === null) {
            throw new UnprocessableEntityHttpException('Local no encontrado.');
        }

        $normalizedLines = [];

        foreach ($data['lines'] as $lineData) {
            if (!is_array($lineData)) {
                throw new UnprocessableEntityHttpException('Linea de pedido no valida.');
            }

            $productId = $this->positiveInteger(
                $lineData['productId'] ?? $lineData['productoId'] ?? null,
                'productId debe ser un entero positivo.'
            );
            $quantity = $this->positiveInteger(
                $lineData['quantity'] ?? $lineData['cantidad'] ?? null,
                'quantity debe ser un entero positivo.'
            );
            $normalizedLines[] = [
                'productId' => $productId,
                'quantity' => $quantity,
                'notes' => $lineData['notes'] ?? null,
            ];
        }

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $order = (new CustomerOrder())
                ->setReference($this->generateReference())
                ->setUser($user)
                ->setLocal($local)
                ->setType((string) $data['type'])
                ->setStatus(PaymentMethod::requiresOnlinePayment($paymentMethod) ? OrderStatus::PENDING : OrderStatus::CONFIRMED)
                ->setPaymentStatus(PaymentStatus::PENDING)
                ->setPaymentMethod($paymentMethod)
                ->setNotes($data['notes'] ?? $data['notas'] ?? null)
                ->setPhone($data['phone'] ?? $data['telefono'] ?? $user->getPhone())
                ->setAddress($data['address'] ?? $data['direccion'] ?? null);

            $total = 0.0;

            foreach ($normalizedLines as $lineData) {
                $product = $this->getProduct($lineData['productId']);
                $order->addLine(
                    (new OrderLine())
                        ->setProduct($product)
                        ->setQuantity($lineData['quantity'])
                        ->setUnitPrice($product->getPrice())
                        ->setNotes($lineData['notes'])
                );
                $total += ((float) $product->getPrice()) * $lineData['quantity'];
            }

            $order->setTotal($total);
            $this->stockService->reserve($local, $order);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $connection->commit();

            return $order;
        } catch (\Throwable $exception) {
            $connection->rollBack();
            $this->entityManager->clear();

            if (!$exception instanceof HttpExceptionInterface) {
                $this->logger->error('Order creation failed.', [
                    'exception' => $exception,
                    'user_id' => $user->getId(),
                    'local_id' => $localId,
                    'payment_method' => $paymentMethod,
                ]);
            }

            throw $exception;
        }
    }

    /** @return CustomerOrder[] */
    public function findVisibleOrders(User $user): array
    {
        if ($this->authorization->isAdmin($user)) {
            return $this->orders->findAllVisible();
        }

        if ($this->authorization->isLocalOperator($user)) {
            $localId = $this->authorization->localId($user);

            if ($localId === null) {
                throw new AccessDeniedHttpException('Usuario operativo sin local asignado.');
            }

            return $this->orders->findVisibleForLocal($localId);
        }

        return $this->orders->findVisibleForUser($user);
    }

    // Devuelve 404 si el pedido no existe y 403 si queda fuera del ámbito del usuario.
    public function getVisibleOrder(int $id, User $user): CustomerOrder
    {
        $order = $this->orders->find($id);

        if ($order === null) {
            throw new NotFoundHttpException('Pedido no encontrado.');
        }

        $this->authorization->assertCanAccessOrder($order, $user, 'No puedes acceder a este pedido.');

        return $order;
    }

    // Cancela y repone inventario de forma transaccional e idempotente.
    public function cancel(int $id, User $user): CustomerOrder
    {
        return $this->cancellation->cancel($id, $user);
    }

    // Cambia el estado del pedido solo si la transición está permitida.
    public function transition(CustomerOrder $order, string $targetStatus): CustomerOrder
    {
        $targetStatus = $this->normalizeStatus($targetStatus);
        $this->validator->orderStatus($targetStatus);

        if ($targetStatus === OrderStatus::CANCELLED) {
            throw new UnprocessableEntityHttpException('La cancelacion debe usar el flujo transaccional de cancelacion.');
        }

        $allowed = OrderStatus::TRANSITIONS[$order->getStatus()] ?? [];

        if (!in_array($targetStatus, $allowed, true)) {
            throw new BadRequestHttpException(
                sprintf('Transicion de %s a %s no permitida.', $order->getStatus(), $targetStatus)
            );
        }

        $order->setStatus($targetStatus);
        $this->entityManager->flush();

        return $order;
    }

    // Permite aceptar estados escritos en español desde el frontend.
    private function normalizeStatus(string $status): string
    {
        return [
            'pendiente'       => OrderStatus::PENDING,
            'confirmado'      => OrderStatus::CONFIRMED,
            'preparando'      => OrderStatus::PREPARING,
            'en preparación'  => OrderStatus::PREPARING,
            'en preparacion'  => OrderStatus::PREPARING,
            'listo'           => OrderStatus::READY,
            'entregado'       => OrderStatus::COMPLETED,
            'cancelado'       => OrderStatus::CANCELLED,
        ][mb_strtolower(trim($status))] ?? $status;
    }

    private function positiveInteger(mixed $value, string $message): int
    {
        if (!is_int($value) && !is_string($value)) {
            throw new UnprocessableEntityHttpException($message);
        }

        $validated = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($validated === false) {
            throw new UnprocessableEntityHttpException($message);
        }

        return $validated;
    }

    // Genera una referencia única para identificar el pedido.
    private function generateReference(): string
    {
        do {
            $reference = 'BAME-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
        } while ($this->orders->findOneBy(['reference' => $reference]) !== null);

        return $reference;
    }

    private function getProduct(int $productId): Product
    {
        $product = $this->products->find($productId);

        if (
            !$product instanceof Product
            || !$product->isAvailable()
            || in_array($product->getAvailability(), [ProductAvailability::HIDDEN, ProductAvailability::SOLD_OUT], true)
        ) {
            throw new UnprocessableEntityHttpException('Producto no disponible.');
        }

        return $product;
    }
}
