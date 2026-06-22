<?php

namespace App\Tests\Functional;

use App\Entity\Category;
use App\Entity\Local;
use App\Entity\LocalProduct;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\LocalStatus;
use App\Enum\ProductAvailability;
use App\Enum\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class ApiTestCase extends WebTestCase
{
    protected function jsonRequest(
        string $method,
        string $uri,
        array $payload = [],
        ?string $token = null,
        ?KernelBrowser $client = null
    ): array {
        self::assertSafeTestDatabaseEnvironment();
        $this->skipSupabaseWriteForUnsafeRequest($method, $uri);

        $client ??= static::createClient();

        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        if ($token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }

        $client->request(
            $method,
            $uri,
            [],
            [],
            $headers,
            json_encode($payload, JSON_THROW_ON_ERROR)
        );

        return [
            'status' => $client->getResponse()->getStatusCode(),
            'json' => json_decode($client->getResponse()->getContent() ?: '{}', true),
            'content' => $client->getResponse()->getContent(),
        ];
    }

    protected function entityManager(): EntityManagerInterface
    {
        self::assertSafeTestDatabaseEnvironment();

        return static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function uniqueEmail(string $prefix = 'user'): string
    {
        return sprintf('%s-%s@example.test', $prefix, bin2hex(random_bytes(6)));
    }

    protected function createUser(
        KernelBrowser $client,
        ?string $email = null,
        string $password = 'Cliente1234!',
        array $roles = [Roles::USER],
        ?Local $local = null
    ): User {
        self::assertSafeTestDatabaseEnvironment();

        $email ??= $this->uniqueEmail('user');
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = (new User())
            ->setName('Usuario Test')
            ->setSurname('Funcional')
            ->setUsername(null)
            ->setEmail($email)
            ->setPhone('+34604265251')
            ->setLocal($local)
            ->setRoles($roles);

        $user->setPassword($hasher->hashPassword($user, $password));

        $em = $this->entityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function loginUser(KernelBrowser $client, User $user, string $password = 'Cliente1234!'): string
    {
        $response = $this->jsonRequest(
            'POST',
            '/api/v1/auth/login',
            [
                'email' => $user->getEmail(),
                'password' => $password,
            ],
            null,
            $client
        );

        return $response['json']['data']['token'] ?? '';
    }

    protected function createCategory(KernelBrowser $client): Category
    {
        self::assertSafeTestDatabaseEnvironment();

        $suffix = bin2hex(random_bytes(5));
        $name = 'Categoria Test '.$suffix;

        $em = $this->entityManager();
        $id = $em->getConnection()->fetchOne(
            <<<'SQL'
                INSERT INTO categories (
                    name, name_en, name_fr, name_de, name_it, slug, description,
                    desc_en, desc_fr, desc_de, desc_it, active, sort_order
                ) VALUES (
                    :name, :name_en, :name_fr, :name_de, :name_it, :slug, :description,
                    :description, :description, :description, :description, true, 1
                )
                RETURNING id
            SQL,
            [
                'name' => $name,
                'name_en' => $name,
                'name_fr' => $name,
                'name_de' => $name,
                'name_it' => $name,
                'slug' => 'categoria-test-'.$suffix,
                'description' => 'Categoria creada para tests funcionales',
            ]
        );

        return $em->find(Category::class, (int) $id);
    }

    protected function createProduct(KernelBrowser $client, ?Category $category = null): Product
    {
        self::assertSafeTestDatabaseEnvironment();

        $category ??= $this->createCategory($client);
        $suffix = bin2hex(random_bytes(5));
        $name = 'Pollo Test '.$suffix;

        $em = $this->entityManager();
        $id = $em->getConnection()->fetchOne(
            <<<'SQL'
                INSERT INTO products (
                    category_id, name, name_en, name_fr, name_de, name_it, slug,
                    description, desc_en, desc_fr, desc_de, desc_it, price, available,
                    availability, featured, weight, prep_time, sort_order
                ) VALUES (
                    :category_id, :name, :name_en, :name_fr, :name_de, :name_it, :slug,
                    :description, :description, :description, :description, :description,
                    :price, true, :availability, false, :weight, :prep_time, 1
                )
                RETURNING id
            SQL,
            [
                'category_id' => $category->getId(),
                'name' => $name,
                'name_en' => $name,
                'name_fr' => $name,
                'name_de' => $name,
                'name_it' => $name,
                'slug' => 'pollo-test-'.$suffix,
                'description' => 'Producto creado para tests funcionales',
                'price' => '9.95',
                'availability' => ProductAvailability::AVAILABLE,
                'weight' => '1 racion',
                'prep_time' => 15,
            ]
        );

        return $em->find(Product::class, (int) $id);
    }

    protected function createLocal(KernelBrowser $client): Local
    {
        self::assertSafeTestDatabaseEnvironment();

        $suffix = bin2hex(random_bytes(5));

        $local = (new Local())
            ->setName('Local Test '.$suffix)
            ->setAddress('Calle Test 1')
            ->setCity('Murcia')
            ->setPostalCode('30001')
            ->setPhone('604265251')
            ->setEmail('local-'.$suffix.'@example.test')
            ->setHours([])
            ->setActive(true)
            ->setStatus(LocalStatus::OPEN);

        $em = $this->entityManager();
        $em->persist($local);
        $em->flush();

        return $local;
    }

    protected function assignProductToLocal(KernelBrowser $client, Product $product, Local $local, int $stock = 10): LocalProduct
    {
        self::assertSafeTestDatabaseEnvironment();

        $localProduct = (new LocalProduct())
            ->setProduct($product)
            ->setLocal($local)
            ->setStock($stock);

        $em = $this->entityManager();
        $em->persist($localProduct);
        $em->flush();

        return $localProduct;
    }

    protected function skipIfSupabaseDatabase(string $reason): void
    {
        if (!self::isSafeTestDatabaseUrl(self::databaseUrl())) {
            self::markTestSkipped($reason.' Configura una DATABASE_URL de test aislada para ejecutar este test.');
        }
    }

    public static function assertSafeTestDatabaseEnvironment(): void
    {
        if ((string) self::envValue('APP_ENV') !== 'test') {
            return;
        }

        if (!self::isSafeTestDatabaseUrl(self::databaseUrl())) {
            throw new \RuntimeException(
                'DATABASE_URL de test no segura. Usa una base local aislada (localhost, 127.0.0.1 o un servicio Docker permitido).'
            );
        }
    }

    public static function isSafeTestDatabaseUrl(string $databaseUrl): bool
    {
        $host = self::databaseHost($databaseUrl);

        if ($host === null || $host === '') {
            return false;
        }

        $allowedHosts = ['localhost', '127.0.0.1', 'db', 'database', 'postgres'];

        if (in_array($host, $allowedHosts, true)) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return in_array($host, ['127.0.0.1', '::1'], true);
        }

        return false;
    }

    private static function databaseUrl(): string
    {
        return (string) (
            $_ENV['DATABASE_URL']
            ?? $_SERVER['DATABASE_URL']
            ?? getenv('DATABASE_URL')
            ?? ''
        );
    }

    private static function envValue(string $key): mixed
    {
        return $_ENV[$key]
            ?? $_SERVER[$key]
            ?? getenv($key)
            ?? null;
    }

    private static function databaseHost(string $databaseUrl): ?string
    {
        $parts = parse_url($databaseUrl);

        if (!is_array($parts)) {
            return null;
        }

        $host = $parts['host'] ?? null;

        if (!is_string($host) || $host === '') {
            return null;
        }

        $host = mb_strtolower($host);

        if (str_contains($host, 'supabase.co') || str_contains($host, 'supabase.com') || str_contains($host, 'pooler.supabase.com')) {
            return null;
        }

        return $host;
    }

    private function skipSupabaseWriteForUnsafeRequest(string $method, string $uri): void
    {
        if (!in_array(mb_strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        if ($uri === '/api/v1/auth/login') {
            $this->skipIfSupabaseDatabase('El login funcional genera tokens y escribiria en Supabase.');
            return;
        }

        $this->skipIfSupabaseDatabase(sprintf(
            'La peticion funcional %s %s podria modificar datos reales en Supabase.',
            mb_strtoupper($method),
            $uri
        ));
    }
}
