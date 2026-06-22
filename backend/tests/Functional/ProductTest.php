<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductTest.php — pruebas funcionales de productos.
//
// Este conjunto de pruebas verifica el acceso público a los productos y
// comprueba que los endpoints de administración respondan correctamente
// cuando el usuario dispone de los permisos necesarios.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Tests\Functional;

final class ProductTest extends ApiTestCase
{
    // Comprueba que el listado de productos puede consultarse sin autenticación.
    public function testProductsListIsPublic(): void
    {
        $client = static::createClient();
        $this->createProduct($client);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/products?q=pollo&available=true',
            [],
            null,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testProductosSpanishAliasListIsPublic(): void
    {
        $client = static::createClient();
        $this->createProduct($client);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/productos?q=pollo&available=true',
            [],
            null,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    // Comprueba el acceso público al detalle de un producto.
    public function testProductDetailIsPublic(): void
    {
        $client = static::createClient();
        $product = $this->createProduct($client);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/products/'.$product->getId(),
            [],
            null,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertSame($product->getId(), $response['json']['data']['id']);
    }

    public function testLocalesSpanishAliasListIsPublic(): void
    {
        $client = static::createClient();
        $this->createLocal($client);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/locales',
            [],
            null,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testCategoriasSpanishAliasListIsPublic(): void
    {
        $client = static::createClient();
        $this->createCategory($client);

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/categorias',
            [],
            null,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    // Comprueba que un administrador puede consultar el listado de productos.
    public function testAdminProductsSpanishAliasListsProducts(): void
    {
        $client = static::createClient();
        $this->createProduct($client);
        $admin = $this->createUser($client, $this->uniqueEmail('admin'), 'Admin1234!', ['ROLE_ADMIN']);
        $token = $this->loginUser($client, $admin, 'Admin1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/productos',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status']);
        self::assertIsArray($response['json']['data']);
    }

    public function testResponsibleCanListAdminProducts(): void
    {
        $client = static::createClient();
        $this->createProduct($client);
        $local = $this->createLocal($client);
        $responsible = $this->createUser(
            $client,
            $this->uniqueEmail('responsable-products'),
            'Cliente1234!',
            ['ROLE_RESPONSABLE'],
            $local
        );
        $token = $this->loginUser($client, $responsible, 'Cliente1234!');

        $response = $this->jsonRequest(
            'GET',
            '/api/v1/admin/productos',
            [],
            $token,
            $client
        );

        self::assertSame(200, $response['status'], $response['content']);
        self::assertTrue($response['json']['success']);
    }
}
