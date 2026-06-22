<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderTest.php — pruebas funcionales de pedidos.
//
// Este conjunto de pruebas verifica las operaciones principales relacionadas
// con los pedidos, asegurando que los usuarios puedan crear pedidos válidos
// y que la API devuelva la información esperada.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Tests\Functional;

use App\Entity\CustomerOrder;
use App\Entity\LocalProduct;
use App\Entity\OrderLine;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;

final class OrderTest extends ApiTestCase
{
    // Comprueba que un usuario autenticado puede crear un pedido.
    public function testUserCanCreateOrder(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order'), 'Cliente1234!');
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $token = $this->loginUser($client, $user, 'Cliente1234!');

        $response = $this->jsonRequest(
            'POST',
            '/api/v1/orders',
            [
                'localId' => $local->getId(),
                'type' => 'takeaway',
                'paymentMethod' => PaymentMethod::PAY_AT_STORE,
                'lines' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 1,
                    ],
                ],
            ],
            $token,
            $client
        );

        self::assertSame(201, $response['status']);

        self::assertSame(OrderStatus::CONFIRMED, $response['json']['data']['status']);
        self::assertSame(PaymentMethod::PAY_AT_STORE, $response['json']['data']['paymentMethod']);
        self::assertSame(PaymentStatus::PENDING, $response['json']['data']['paymentStatus']);

        $updatedStock = $this->entityManager()->find(LocalProduct::class, $stock->getId());
        self::assertSame(4, $updatedStock->getStock());
    }

    public function testUserCanCreateOrderViaSpanishAlias(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order-alias'), 'Cliente1234!');
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 3);
        $token = $this->loginUser($client, $user, 'Cliente1234!');

        $response = $this->jsonRequest(
            'POST',
            '/api/v1/pedidos',
            [
                'localId' => $local->getId(),
                'type' => 'takeaway',
                'lines' => [
                    [
                        'productId' => $product->getId(),
                        'quantity' => 1,
                    ],
                ],
            ],
            $token,
            $client
        );

        self::assertSame(201, $response['status']);
        self::assertSame(2, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }

    public function testOrderWithProductOutsideSelectedLocalReturnsUnprocessableEntity(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order-wrong-local'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $token = $this->loginUser($client, $user);

        $response = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [[
                'productId' => $product->getId(),
                'quantity' => 1,
            ]],
        ], $token, $client);

        self::assertSame(422, $response['status']);
    }

    public function testOrderWithInsufficientStockReturnsUnprocessableEntity(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order-insufficient-stock'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $this->assignProductToLocal($client, $product, $local, 1);
        $token = $this->loginUser($client, $user);

        $response = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [[
                'productId' => $product->getId(),
                'quantity' => 2,
            ]],
        ], $token, $client);

        self::assertSame(422, $response['status']);
    }

    public function testOrderWithInvalidQuantityReturnsUnprocessableEntity(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order-invalid-quantity'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $this->assignProductToLocal($client, $product, $local, 5);
        $token = $this->loginUser($client, $user);

        $response = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [[
                'productId' => $product->getId(),
                'quantity' => 0,
            ]],
        ], $token, $client);

        self::assertSame(422, $response['status']);
    }

    public function testFailureInOneLineRollsBackWholeOrderAndStock(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('order-rollback'));
        $local = $this->createLocal($client);
        $productWithStock = $this->createProduct($client);
        $productWithoutEnoughStock = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $productWithStock, $local, 5);
        $this->assignProductToLocal($client, $productWithoutEnoughStock, $local, 1);
        $token = $this->loginUser($client, $user);
        $ordersBefore = $this->entityManager()->getRepository(CustomerOrder::class)->count([]);

        $response = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [
                [
                    'productId' => $productWithStock->getId(),
                    'quantity' => 2,
                ],
                [
                    'productId' => $productWithoutEnoughStock->getId(),
                    'quantity' => 2,
                ],
            ],
        ], $token, $client);

        self::assertSame(422, $response['status']);
        self::assertSame(
            $ordersBefore,
            $this->entityManager()->getRepository(CustomerOrder::class)->count([])
        );

        $unchangedStock = $this->entityManager()->find(LocalProduct::class, $stock->getId());
        self::assertSame(5, $unchangedStock->getStock());
    }

    public function testCancellingOrderRestoresStockOnlyOnce(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('cancel-stock'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $token = $this->loginUser($client, $user);

        $createResponse = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [[
                'productId' => $product->getId(),
                'quantity' => 2,
            ]],
        ], $token, $client);
        $orderId = $createResponse['json']['data']['id'];

        self::assertSame(201, $createResponse['status']);
        self::assertSame(3, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());

        $firstCancellation = $this->jsonRequest('PATCH', '/api/v1/orders/'.$orderId.'/cancel', [], $token, $client);

        self::assertSame(200, $firstCancellation['status']);
        self::assertSame(OrderStatus::CANCELLED, $firstCancellation['json']['data']['status']);
        self::assertSame(PaymentStatus::FAILED, $firstCancellation['json']['data']['paymentStatus']);
        self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());

        $secondCancellation = $this->jsonRequest('PATCH', '/api/v1/orders/'.$orderId.'/cancel', [], $token, $client);

        self::assertSame(200, $secondCancellation['status']);
        self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }

    public function testCancellingUnknownOrderReturnsNotFound(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('cancel-missing'));
        $token = $this->loginUser($client, $user);

        $response = $this->jsonRequest('PATCH', '/api/v1/orders/2147483647/cancel', [], $token, $client);

        self::assertSame(404, $response['status']);
    }

    public function testPaidOnlineCancellationKeepsPaidStatusAndRestoresStock(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('cancel-paid-online'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 4);
        $token = $this->loginUser($client, $user);

        $order = (new CustomerOrder())
            ->setReference('PAID-'.strtoupper(bin2hex(random_bytes(8))))
            ->setUser($user)
            ->setLocal($local)
            ->setStatus(OrderStatus::CONFIRMED)
            ->setPaymentMethod(PaymentMethod::STRIPE)
            ->setPaymentStatus(PaymentStatus::PAID)
            ->setStripeCheckoutSessionId('cs_test_'.bin2hex(random_bytes(8)))
            ->setTotal($product->getPrice());
        $order->addLine(
            (new OrderLine())
                ->setProduct($product)
                ->setQuantity(1)
                ->setUnitPrice($product->getPrice())
        );

        $em = $this->entityManager();
        $em->persist($order);
        $em->flush();

        $response = $this->jsonRequest('PATCH', '/api/v1/orders/'.$order->getId().'/cancel', [], $token, $client);

        self::assertSame(200, $response['status']);
        self::assertSame(OrderStatus::CANCELLED, $response['json']['data']['status']);
        self::assertSame(PaymentStatus::PAID, $response['json']['data']['paymentStatus']);
        self::assertSame(5, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }

    public function testOrderInPreparingStateCannotBeCancelled(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('cancel-preparing'));
        $local = $this->createLocal($client);
        $product = $this->createProduct($client);
        $stock = $this->assignProductToLocal($client, $product, $local, 5);
        $token = $this->loginUser($client, $user);

        $createResponse = $this->jsonRequest('POST', '/api/v1/orders', [
            'localId' => $local->getId(),
            'type' => 'takeaway',
            'lines' => [[
                'productId' => $product->getId(),
                'quantity' => 1,
            ]],
        ], $token, $client);
        $order = $this->entityManager()->find(CustomerOrder::class, $createResponse['json']['data']['id']);
        $order->setStatus(OrderStatus::PREPARING);
        $this->entityManager()->flush();

        $response = $this->jsonRequest('PATCH', '/api/v1/orders/'.$order->getId().'/cancel', [], $token, $client);

        self::assertSame(422, $response['status']);
        self::assertSame(4, $this->entityManager()->find(LocalProduct::class, $stock->getId())->getStock());
    }
}
