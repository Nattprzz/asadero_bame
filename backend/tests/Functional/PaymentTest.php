<?php

namespace App\Tests\Functional;

use App\Entity\CustomerOrder;
use App\Entity\LocalProduct;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Repository\StripeEventLedgerRepository;
use App\Repository\LocalProductRepository;
use App\Repository\LocalRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\InputValidator;
use App\Service\CheckoutOriginValidator;
use App\Service\CheckoutSessionService;
use App\Service\OrderStockService;
use App\Service\StripeConfigValidator;
use App\Service\StripeEventLedgerService;
use App\Service\StripePaymentService;
use App\Service\StripeWebhookHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class PaymentTest extends ApiTestCase
{
    public function testPayAtStoreCreatesConfirmedOrderWithoutStripe(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('pay-store'));
        $token = $this->loginUser($client, $user);
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);

        $response = $this->jsonRequest(
            'POST',
            '/api/payments/stripe/create-checkout-session',
            [
                'localId' => $local->getId(),
                'paymentMethod' => PaymentMethod::PAY_AT_STORE,
                'type' => 'takeaway',
                'items' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 2,
                    ],
                ],
            ],
            $token,
            $client
        );

        self::assertSame(201, $response['status'], $response['content']);
        self::assertTrue($response['json']['success']);
        self::assertSame(PaymentMethod::PAY_AT_STORE, $response['json']['data']['paymentMethod']);
        self::assertSame(PaymentStatus::PENDING, $response['json']['data']['paymentStatus']);
        self::assertFalse($response['json']['data']['requiresOnlinePayment']);
        self::assertArrayNotHasKey('checkoutUrl', $response['json']['data']);
        self::assertSame(OrderStatus::CONFIRMED, $response['json']['data']['order']['status']);
        self::assertSame(PaymentMethod::PAY_AT_STORE, $response['json']['data']['order']['paymentMethod']);
        self::assertSame(19.9, $response['json']['data']['order']['total']);

        $updatedStock = $this->entityManager()->find($stock::class, $stock->getId());
        self::assertSame(3, $updatedStock->getStock());
    }

    public function testCheckoutRequiresPaymentMethod(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('pay-method-required'));
        $token = $this->loginUser($client, $user);
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $this->assignProductToLocal($client, $product, $local, 5);

        $response = $this->jsonRequest(
            'POST',
            '/api/payments/stripe/create-checkout-session',
            [
                'localId' => $local->getId(),
                'type' => 'takeaway',
                'items' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 1,
                    ],
                ],
            ],
            $token,
            $client
        );

        self::assertSame(422, $response['status'], $response['content']);
        self::assertFalse($response['json']['success']);
        self::assertSame('VALIDATION_ERROR', $response['json']['error']['code']);
        self::assertSame(['Este campo es obligatorio.'], $response['json']['error']['details']['paymentMethod']);
    }

    public function testOnlineCheckoutReservesGroupedStockBeforePayment(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-reservation'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $service = $this->stripePaymentService();
        $this->fakeStripeCheckoutSuccess();

        try {
            $result = $service->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [
                    ['productId' => $product->getId(), 'quantity' => 1],
                    ['productId' => $product->getId(), 'quantity' => 2],
                ]
            ));
        } finally {
            ApiRequestor::setHttpClient(null);
        }

        self::assertTrue($result['requiresOnlinePayment']);
        self::assertSame(PaymentStatus::PENDING, $result['order']->getPaymentStatus());
        self::assertSame(OrderStatus::PENDING, $result['order']->getStatus());
        self::assertCount(1, $result['order']->getLines());
        self::assertSame(3, $result['order']->getLines()->first()->getQuantity());
        self::assertSame(2, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        self::assertNotNull($result['order']->getCheckoutIdempotencyKey());
        self::assertNotNull($result['order']->getStripeCheckoutUrl());
    }

    public function testRepeatedCheckoutWithSameIdempotencyKeyReusesOrderAndReservation(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-idempotency'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $service = $this->stripePaymentService();
        $payload = $this->onlineCheckoutPayload(
            $local->getId(),
            [['productId' => $product->getId(), 'quantity' => 2]]
        );
        $ordersBefore = $this->entityManager()->getRepository(CustomerOrder::class)->count([]);
        $this->fakeStripeCheckoutSuccess();

        try {
            $first = $service->createCheckoutSession($user, $payload, 'checkout-retry-1');
            $stockAfterFirstRequest = $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock();
            $second = $this->stripePaymentService()->createCheckoutSession($user, $payload, 'checkout-retry-1');
        } finally {
            ApiRequestor::setHttpClient(null);
        }

        self::assertSame($first['order']->getId(), $second['order']->getId());
        self::assertSame($first['order']->getStripeCheckoutSessionId(), $second['order']->getStripeCheckoutSessionId());
        self::assertSame($first['checkoutUrl'], $second['checkoutUrl']);
        self::assertSame($ordersBefore + 1, $this->entityManager()->getRepository(CustomerOrder::class)->count([]));
        self::assertSame(3, $stockAfterFirstRequest);
        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }

    public function testOnlineCheckoutWithInsufficientStockReturnsUnprocessableEntity(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-no-stock'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 1);
        $ordersBefore = $this->entityManager()->getRepository(CustomerOrder::class)->count([]);

        try {
            $this->stripePaymentService()->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [['productId' => $product->getId(), 'quantity' => 2]]
            ));
            self::fail('Expected insufficient stock validation failure.');
        } catch (UnprocessableEntityHttpException) {
            self::assertSame($ordersBefore, $this->entityManager()->getRepository(CustomerOrder::class)->count([]));
            self::assertSame(1, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        }
    }

    public function testStripeSessionFailureRollsBackOrderAndReservation(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-failure'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $ordersBefore = $this->entityManager()->getRepository(CustomerOrder::class)->count([]);
        $this->fakeStripeCheckoutFailure();

        try {
            $this->stripePaymentService()->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [['productId' => $product->getId(), 'quantity' => 2]]
            ));
            self::fail('Expected Stripe session creation failure.');
        } catch (\Stripe\Exception\ApiErrorException) {
            self::assertSame($ordersBefore, $this->entityManager()->getRepository(CustomerOrder::class)->count([]));
            self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        } finally {
            ApiRequestor::setHttpClient(null);
        }
    }

    public function testSuccessfulAndDuplicateWebhookNeverDiscountStockAgain(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-webhook'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $service = $this->stripePaymentService();
        $this->fakeStripeCheckoutSuccess();

        try {
            $result = $service->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [['productId' => $product->getId(), 'quantity' => 2]]
            ));
        } finally {
            ApiRequestor::setHttpClient(null);
        }

        $order = $result['order'];
        $payload = $this->stripeWebhookPayload('checkout.session.completed', $order, 'paid');
        $signature = $this->stripeSignature($payload, 'whsec_test');
        $ledgerRepository = $this->entityManager()->getRepository(\App\Entity\StripeEventLedger::class);
        $ledgerCountBefore = $ledgerRepository->count([]);

        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());

        $service->handleWebhook($payload, $signature);
        self::assertSame(PaymentStatus::PAID, $order->getPaymentStatus());
        self::assertSame(OrderStatus::CONFIRMED, $order->getStatus());
        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        self::assertSame($ledgerCountBefore + 1, $ledgerRepository->count([]));

        $service->handleWebhook($payload, $signature);
        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        self::assertSame($ledgerCountBefore + 1, $ledgerRepository->count([]));
    }

    #[DataProvider('invalidCompletedSessionProvider')]
    public function testCompletedWebhookMismatchDoesNotConfirmOrder(string $field, mixed $value): void
    {
        [$service, $order, $stock] = $this->pendingStripeOrderForWebhook('stripe-mismatch-'.$field);
        $payload = $this->stripeWebhookPayload(
            'checkout.session.completed',
            $order,
            'paid',
            [$field => $value]
        );

        try {
            $service->handleWebhook($payload, $this->stripeSignature($payload, 'whsec_test'));
            self::fail('Expected Stripe webhook mismatch validation failure.');
        } catch (BadRequestHttpException) {
            $persistedOrder = $this->entityManager()->find(CustomerOrder::class, $order->getId());
            self::assertSame(PaymentStatus::PENDING, $persistedOrder->getPaymentStatus());
            self::assertSame(OrderStatus::PENDING, $persistedOrder->getStatus());
            self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        }
    }

    public static function invalidCompletedSessionProvider(): array
    {
        return [
            'payment status is not paid' => ['payment_status', 'unpaid'],
            'amount does not match' => ['amount_total', 1],
            'currency does not match' => ['currency', 'usd'],
            'mode is not payment' => ['mode', 'subscription'],
            'session id does not match' => ['id', 'cs_test_incorrect'],
            'metadata order id does not match' => ['metadata_order_id', 999999999],
        ];
    }

    public function testExpiredWebhookWithWrongSessionIdDoesNotReleaseStock(): void
    {
        [$service, $order, $stock] = $this->pendingStripeOrderForWebhook('stripe-expired-mismatch');
        $payload = $this->stripeWebhookPayload(
            'checkout.session.expired',
            $order,
            'unpaid',
            ['id' => 'cs_test_incorrect']
        );

        try {
            $service->handleWebhook($payload, $this->stripeSignature($payload, 'whsec_test'));
            self::fail('Expected Stripe Session identity validation failure.');
        } catch (BadRequestHttpException) {
            $persistedOrder = $this->entityManager()->find(CustomerOrder::class, $order->getId());
            self::assertSame(PaymentStatus::PENDING, $persistedOrder->getPaymentStatus());
            self::assertSame(OrderStatus::PENDING, $persistedOrder->getStatus());
            self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
        }
    }

    public function testInvalidStripeEventIsRejectedWithoutProcessingLedger(): void
    {
        [$service, $order, $stock] = $this->pendingStripeOrderForWebhook('stripe-invalid-event');
        $ledgerRepository = $this->entityManager()->getRepository(\App\Entity\StripeEventLedger::class);
        $ledgerCountBefore = $ledgerRepository->count([]);
        $payload = json_encode([
            'id' => '',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $order->getStripeCheckoutSessionId(),
                    'object' => 'checkout.session',
                    'payment_status' => 'paid',
                    'mode' => 'payment',
                    'currency' => 'eur',
                    'amount_total' => (int) round(((float) $order->getTotal()) * 100),
                    'metadata' => ['order_id' => (string) $order->getId()],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        try {
            $service->handleWebhook($payload, $this->stripeSignature($payload, 'whsec_test'));
            self::fail('Expected invalid Stripe event rejection.');
        } catch (BadRequestHttpException) {
            self::assertSame($ledgerCountBefore, $ledgerRepository->count([]));
            self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
            self::assertSame(PaymentStatus::PENDING, $this->entityManager()->find(CustomerOrder::class, $order->getId())->getPaymentStatus());
        }
    }

    public function testExpiredCheckoutReleasesReservationOnlyOnce(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('stripe-expired'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $service = $this->stripePaymentService();
        $this->fakeStripeCheckoutSuccess();

        try {
            $result = $service->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [['productId' => $product->getId(), 'quantity' => 2]]
            ));
        } finally {
            ApiRequestor::setHttpClient(null);
        }

        $order = $result['order'];
        $payload = $this->stripeWebhookPayload('checkout.session.expired', $order, 'unpaid');
        $signature = $this->stripeSignature($payload, 'whsec_test');

        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());

        $service->handleWebhook($payload, $signature);
        self::assertSame(OrderStatus::CANCELLED, $order->getStatus());
        self::assertSame(PaymentStatus::FAILED, $order->getPaymentStatus());
        self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());

        $service->handleWebhook($payload, $signature);
        self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }

    private function stripePaymentService(): StripePaymentService
    {
        $container = static::getContainer();
        $entityManager = $this->entityManager();
        $logger = $container->get(\Psr\Log\LoggerInterface::class);
        $stockService = new OrderStockService(
            $container->get(LocalProductRepository::class)
        );
        $configValidator = new StripeConfigValidator('test');
        $originValidator = new CheckoutOriginValidator('test', 'http://localhost');
        $ledgerService = new StripeEventLedgerService(
            $entityManager,
            $container->get(StripeEventLedgerRepository::class)
        );
        $checkoutSessions = new CheckoutSessionService(
            $entityManager,
            $container->get(LocalRepository::class),
            $container->get(ProductRepository::class),
            $container->get(OrderRepository::class),
            $container->get(InputValidator::class),
            $stockService,
            $configValidator,
            $originValidator,
            $logger,
            'sk_test_fake'
        );
        $webhooks = new StripeWebhookHandler(
            $entityManager,
            $container->get(OrderRepository::class),
            $stockService,
            $configValidator,
            $ledgerService,
            $logger,
            'whsec_test'
        );

        return new StripePaymentService($checkoutSessions, $webhooks);
    }

    private function onlineCheckoutPayload(int $localId, array $items): array
    {
        return [
            'localId' => $localId,
            'paymentMethod' => PaymentMethod::STRIPE,
            'type' => 'takeaway',
            'items' => $items,
            'successUrl' => 'http://localhost/success',
            'cancelUrl' => 'http://localhost/cancel',
        ];
    }

    private function fakeStripeCheckoutSuccess(): void
    {
        ApiRequestor::setHttpClient(new class implements ClientInterface {
            private ?string $sessionId = null;

            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null): array
            {
                $this->sessionId ??= 'cs_test_'.bin2hex(random_bytes(8));

                return [json_encode([
                    'id' => $this->sessionId,
                    'object' => 'checkout.session',
                    'url' => 'https://checkout.stripe.test/session',
                ], JSON_THROW_ON_ERROR), 200, []];
            }
        });
    }

    private function fakeStripeCheckoutFailure(): void
    {
        ApiRequestor::setHttpClient(new class implements ClientInterface {
            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null): array
            {
                return [json_encode([
                    'error' => ['message' => 'Forced Stripe failure', 'type' => 'api_error'],
                ], JSON_THROW_ON_ERROR), 500, []];
            }
        });
    }

    private function stripeWebhookPayload(
        string $type,
        CustomerOrder $order,
        string $paymentStatus,
        array $overrides = []
    ): string
    {
        $metadataOrderId = $overrides['metadata_order_id'] ?? $order->getId();
        unset($overrides['metadata_order_id']);

        $session = array_replace([
            'id' => $order->getStripeCheckoutSessionId(),
            'object' => 'checkout.session',
            'payment_status' => $paymentStatus,
            'mode' => 'payment',
            'currency' => 'eur',
            'amount_total' => (int) round(((float) $order->getTotal()) * 100),
            'metadata' => ['order_id' => (string) $metadataOrderId],
        ], $overrides);

        return json_encode([
            'id' => 'evt_test_'.bin2hex(random_bytes(8)),
            'object' => 'event',
            'type' => $type,
            'data' => [
                'object' => $session,
            ],
        ], JSON_THROW_ON_ERROR);
    }

    /** @return array{StripePaymentService, CustomerOrder, LocalProduct} */
    private function pendingStripeOrderForWebhook(string $emailPrefix): array
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail($emailPrefix));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $service = $this->stripePaymentService();
        $this->fakeStripeCheckoutSuccess();

        try {
            $result = $service->createCheckoutSession($user, $this->onlineCheckoutPayload(
                $local->getId(),
                [['productId' => $product->getId(), 'quantity' => 2]]
            ));
        } finally {
            ApiRequestor::setHttpClient(null);
        }

        return [$service, $result['order'], $stock];
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $timestamp = time();

        return sprintf('t=%d,v1=%s', $timestamp, hash_hmac('sha256', $timestamp.'.'.$payload, $secret));
    }
}
