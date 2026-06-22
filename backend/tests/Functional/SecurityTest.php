<?php

// ─────────────────────────────────────────────────────────────────────────────
// SecurityTest.php — pruebas funcionales de seguridad.
//
// Este conjunto de pruebas verifica que las rutas protegidas de la aplicación
// respeten los permisos definidos y bloqueen los accesos no autorizados.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Tests\Functional;

use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Entity\User;
use App\Enum\Roles;
use PHPUnit\Framework\Attributes\DataProvider;

final class SecurityTest extends ApiTestCase
{
    // Comprueba que una ruta protegida requiere autenticación.
    public function testProtectedEndpointWithoutTokenReturnsUnauthorized(): void
    {
        $response = $this->jsonRequest(
            'GET',
            '/api/v1/auth/me'
        );

        self::assertSame(401, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('UNAUTHORIZED', $response['json']['error']['code']);
    }

    // Comprueba que un usuario normal no puede acceder a rutas de administración.
    public function testAdminEndpointWithUserTokenReturnsForbidden(): void
    {
        $client = static::createClient();
        $user = $this->createUser($client, $this->uniqueEmail('security-user'), 'Cliente1234!', ['ROLE_USER']);
        $token = $this->loginUser($client, $user, 'Cliente1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/users',
            [],
            $token,
            $client
        );

        self::assertSame(403, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('FORBIDDEN', $response['json']['error']['code']);
    }

    public function testResponsibleCannotAccessAdminMetrics(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $responsible = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-metrics'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $local
        );
        $token = $this->loginUser($client, $responsible, 'Cliente1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/metrics',
            [],
            $token,
            $client
        );

        self::assertSame(403, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('FORBIDDEN', $response['json']['error']['code']);
    }

    public function testAdminCanAccessAdminMetrics(): void
    {
        $client = static::createClient();
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-metrics'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/metrics',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status'], $response['content']);
        self::assertTrue($response['json']['success']);
    }

    #[DataProvider('adminLocalCrudCases')]
    public function testAdminCanAccessLocalsCrud(string $method, string $uri, array $payload, int $expectedStatus): void
    {
        $client = static::createClient();
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-locals'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        if (str_contains($uri, '/1')) {
            $local = $this->createLocal($client);
            $uri = str_replace('/1', '/'.$local->getId(), $uri);
        }

        $response = $this->jsonRequest($method, $uri, $payload, $token, $client);

        self::assertSame($expectedStatus, $response['status'], $response['content']);
        self::assertTrue($response['json']['success']);
    }

    public function testRoleUserCannotAccessLocalsCrud(): void
    {
        $client = static::createClient();
        $user = $this->createUser(
            $client,
            $this->uniqueEmail('user-locals'),
            'Cliente1234!',
            [Roles::USER]
        );
        $token = $this->loginUser($client, $user, 'Cliente1234!');

        foreach (self::adminLocalCrudCases() as [$method, $uri, $payload]) {
            if (str_contains($uri, '/1')) {
                $local = $this->createLocal($client);
                $uri = str_replace('/1', '/'.$local->getId(), $uri);
            }

            $response = $this->jsonRequest($method, $uri, $payload, $token, $client);

            self::assertSame(403, $response['status'], $response['content']);
            self::assertFalse($response['json']['success']);
            self::assertSame('FORBIDDEN', $response['json']['error']['code']);
        }
    }

    public function testResponsibleCannotAccessLocalsCrud(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $responsible = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-locals'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $local
        );
        $token = $this->loginUser($client, $responsible, 'Cliente1234!');

        foreach (self::adminLocalCrudCases() as [$method, $uri, $payload]) {
            if (str_contains($uri, '/1')) {
                $local = $this->createLocal($client);
                $uri = str_replace('/1', '/'.$local->getId(), $uri);
            }

            $response = $this->jsonRequest($method, $uri, $payload, $token, $client);

            self::assertSame(403, $response['status'], $response['content']);
            self::assertFalse($response['json']['success']);
            self::assertSame('FORBIDDEN', $response['json']['error']['code']);
        }
    }

    public function testGerenteCannotAccessLocalsCrud(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $gerente = $this->createUser(
            $client,
            $this->uniqueEmail('gerente-locals'),
            'Cliente1234!',
            [Roles::GERENTE],
            $local
        );
        $token = $this->loginUser($client, $gerente, 'Cliente1234!');

        foreach (self::adminLocalCrudCases() as [$method, $uri, $payload]) {
            if (str_contains($uri, '/1')) {
                $local = $this->createLocal($client);
                $uri = str_replace('/1', '/'.$local->getId(), $uri);
            }

            $response = $this->jsonRequest($method, $uri, $payload, $token, $client);

            self::assertSame(403, $response['status'], $response['content']);
            self::assertFalse($response['json']['success']);
            self::assertSame('FORBIDDEN', $response['json']['error']['code']);
        }
    }

    public function testAnonymousCannotAccessLocalsCrud(): void
    {
        foreach (self::adminLocalCrudCases() as [$method, $uri, $payload]) {
            $response = $this->jsonRequest($method, $uri, $payload);

            self::assertSame(401, $response['status'], $response['content']);
            self::assertFalse($response['json']['success']);
            self::assertSame('UNAUTHORIZED', $response['json']['error']['code']);
        }
    }

    // Comprueba que el proceso de pago requiere un usuario autenticado.
    public function testStripeCheckoutWithoutTokenReturnsUnauthorized(): void
    {
        $response = $this->jsonRequest(
            'POST',
            '/api/payments/stripe/create-checkout-session',
            [
                'localId' => 1,
                'items' => [
                    [
                        'productId' => 1,
                        'quantity' => 1,
                    ],
                ],
                'successUrl' => 'http://localhost/success',
                'cancelUrl' => 'http://localhost/cancel',
            ]
        );

        self::assertSame(401, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('UNAUTHORIZED', $response['json']['error']['code']);
    }

    public function testUnknownApiRouteReturnsJson404(): void
    {
        $response = $this->jsonRequest('GET', '/api/v1/this-route-does-not-exist');

        self::assertSame(404, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('NOT_FOUND', $response['json']['error']['code']);
        self::assertStringNotContainsString('<html', mb_strtolower($response['content']));
    }

    public function testMalformedJsonReturnsJson400(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v1/auth/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            '{"email":'
        );

        $response = [
            'status' => $client->getResponse()->getStatusCode(),
            'json' => json_decode($client->getResponse()->getContent() ?: '{}', true),
            'content' => $client->getResponse()->getContent(),
        ];

        self::assertSame(400, $response['status']);
        self::assertFalse($response['json']['success']);
        self::assertSame('BAD_REQUEST', $response['json']['error']['code']);
        self::assertStringNotContainsString('<html', mb_strtolower($response['content']));
    }

    public function testResponsibleCannotQueryAnotherLocalOrders(): void
    {
        $client = static::createClient();
        $ownLocal = $this->createLocal($client);
        $otherLocal = $this->createLocal($client);
        $responsible = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-security'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $ownLocal
        );
        $token = $this->loginUser($client, $responsible);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/orders?localId='.$otherLocal->getId(),
            [],
            $token,
            $client
        );

        self::assertSame(403, $response['status']);
    }

    public function testAdminCanQueryOrdersWithoutLocalFilter(): void
    {
        $client = static::createClient();
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-global'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/orders',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testAdminCanQueryOrdersViaSpanishAlias(): void
    {
        $client = static::createClient();
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-orders-alias'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/pedidos',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testAdminCanAccessLocalesViaSpanishAlias(): void
    {
        $client = static::createClient();
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-locales-alias'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/locales',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testResponsibleIsRestrictedToAssignedLocalForListDetailAndCancellation(): void
    {
        $client = static::createClient();
        $localA = $this->createLocal($client);
        $localB = $this->createLocal($client);
        $customerA = $this->createUser($client, $this->uniqueEmail('customer-local-a'));
        $customerB = $this->createUser($client, $this->uniqueEmail('customer-local-b'));
        $responsible = $this->createUser(
            $client,
            $this->uniqueEmail('responsible-local-a'),
            'Cliente1234!',
            [Roles::RESPONSABLE],
            $localA
        );
        $orderA = $this->createOrderFor($customerA, $localA);
        $orderB = $this->createOrderFor($customerB, $localB);
        $token = $this->loginUser($client, $responsible);

        $listResponse = $this->jsonRequest('GET', '/api/v1/orders', [], $token, $client);
        $visibleIds = array_column($listResponse['json']['data'] ?? [], 'id');

        self::assertSame(200, $listResponse['status']);
        self::assertContains($orderA->getId(), $visibleIds);
        self::assertNotContains($orderB->getId(), $visibleIds);

        $detailResponse = $this->jsonRequest('GET', '/api/v1/orders/'.$orderB->getId(), [], $token, $client);
        self::assertSame(403, $detailResponse['status']);

        $cancelResponse = $this->jsonRequest('PATCH', '/api/v1/orders/'.$orderB->getId().'/cancel', [], $token, $client);
        self::assertSame(403, $cancelResponse['status']);
    }

    public function testAdminCanSeeOrdersFromEveryLocal(): void
    {
        $client = static::createClient();
        $localA = $this->createLocal($client);
        $localB = $this->createLocal($client);
        $customerA = $this->createUser($client, $this->uniqueEmail('admin-customer-a'));
        $customerB = $this->createUser($client, $this->uniqueEmail('admin-customer-b'));
        $admin = $this->createUser(
            $client,
            $this->uniqueEmail('admin-all-locals'),
            'Admin1234!',
            [Roles::ADMIN]
        );
        $orderA = $this->createOrderFor($customerA, $localA);
        $orderB = $this->createOrderFor($customerB, $localB);
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $listResponse = $this->jsonRequest('GET', '/api/v1/orders', [], $token, $client);
        $visibleIds = array_column($listResponse['json']['data'] ?? [], 'id');

        self::assertSame(200, $listResponse['status']);
        self::assertContains($orderA->getId(), $visibleIds);
        self::assertContains($orderB->getId(), $visibleIds);
        self::assertSame(200, $this->jsonRequest('GET', '/api/v1/orders/'.$orderA->getId(), [], $token, $client)['status']);
        self::assertSame(200, $this->jsonRequest('GET', '/api/v1/orders/'.$orderB->getId(), [], $token, $client)['status']);
    }

    public function testCustomerCanOnlyAccessAndCancelOwnOrders(): void
    {
        $client = static::createClient();
        $local = $this->createLocal($client);
        $customer = $this->createUser($client, $this->uniqueEmail('customer-own-order'));
        $otherCustomer = $this->createUser($client, $this->uniqueEmail('other-customer-order'));
        $ownOrder = $this->createOrderFor($customer, $local);
        $otherOrder = $this->createOrderFor($otherCustomer, $local);
        $token = $this->loginUser($client, $customer);

        $listResponse = $this->jsonRequest('GET', '/api/v1/orders', [], $token, $client);
        $visibleIds = array_column($listResponse['json']['data'] ?? [], 'id');

        self::assertSame(200, $listResponse['status']);
        self::assertContains($ownOrder->getId(), $visibleIds);
        self::assertNotContains($otherOrder->getId(), $visibleIds);
        self::assertSame(200, $this->jsonRequest('GET', '/api/v1/orders/'.$ownOrder->getId(), [], $token, $client)['status']);
        self::assertSame(403, $this->jsonRequest('GET', '/api/v1/orders/'.$otherOrder->getId(), [], $token, $client)['status']);
        self::assertSame(200, $this->jsonRequest('PATCH', '/api/v1/orders/'.$ownOrder->getId().'/cancel', [], $token, $client)['status']);
        self::assertSame(403, $this->jsonRequest('PATCH', '/api/v1/orders/'.$otherOrder->getId().'/cancel', [], $token, $client)['status']);
    }

    private function createOrderFor(User $user, Local $local): CustomerOrder
    {
        $this->skipIfSupabaseDatabase('El test intentaria crear un pedido en Supabase.');

        $order = (new CustomerOrder())
            ->setReference('TEST-'.strtoupper(bin2hex(random_bytes(8))))
            ->setUser($user)
            ->setLocal($local)
            ->setTotal('0.00');

        $em = $this->entityManager();
        $em->persist($order);
        $em->flush();

        return $order;
    }

    public static function adminLocalCrudCases(): array
    {
        return [
            'list' => ['GET', '/api/v1/admin/locals', [], 200],
            'create' => ['POST', '/api/v1/admin/locals', [
                'name' => 'Local Admin Test',
                'address' => 'Calle Admin 1',
                'city' => 'Murcia',
                'postalCode' => '30001',
                'phone' => '604265251',
                'email' => 'local-admin@example.test',
                'hours' => [],
                'active' => true,
                'status' => 'open',
                'whatsapp' => '604265251',
            ], 201],
            'update' => ['PATCH', '/api/v1/admin/locals/1', [
                'name' => 'Local Admin Test 2',
                'status' => 'open',
            ], 200],
            'delete' => ['DELETE', '/api/v1/admin/locals/1', [], 200],
        ];
    }
}
