<?php

// ─────────────────────────────────────────────────────────────────────────────
// AppFixtures.php — datos iniciales de prueba.
//
// Carga usuarios, categorías, alérgenos, productos, locales, stock y un pedido
// de ejemplo para poder probar la aplicación con datos reales desde el inicio.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\DataFixtures;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Entity\LocalProduct;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Enum\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Ejecuta la carga completa de datos iniciales.
    // ─────────────────────────────────────────────────────────────────────────

    public function load(ObjectManager $manager): void
    {
        $users = $this->loadUsers($manager);
        [$categories, $allergens] = $this->loadCatalogBase($manager);
        $products = $this->loadProducts($manager, $categories, $allergens);
        $local = $this->loadLocalsAndStock($manager, $products);

        $this->loadOrders($manager, $users, $local, $products);

        $manager->flush();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea los usuarios básicos para poder acceder y probar la aplicación.
    // ─────────────────────────────────────────────────────────────────────────

    private function loadUsers(ObjectManager $manager): array
    {
        $users = [];

        foreach ([
            ['admin', 'Admin', 'BAME', 'admin@bame.test', 'Admin1234!', [Roles::ADMIN]],
            ['cliente', 'Cliente', 'Demo', 'cliente@bame.test', 'Cliente1234!', [Roles::USER]],
        ] as [$username, $name, $surname, $email, $password, $roles]) {
            $user = (new User())
                ->setUsername($username)
                ->setName($name)
                ->setSurname($surname)
                ->setEmail($email)
                ->setRoles($roles);

            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $password)
            );

            $manager->persist($user);

            $users[$username] = $user;
        }

        return $users;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea las categorías y alérgenos base usados por el catálogo.
    // ─────────────────────────────────────────────────────────────────────────

    private function loadCatalogBase(ObjectManager $manager): array
    {
        $categories = [];

        foreach ([
            ['pollos-asados', 'Pollos asados', 'Pollos asados al estilo Asadero BAME.', 10],
            ['carnes', 'Carnes', 'Carnes asadas y raciones principales.', 20],
            ['guarniciones', 'Guarniciones', 'Acompanamientos para compartir.', 30],
            ['bebidas', 'Bebidas', 'Bebidas frias para pedidos para llevar.', 40],
            ['postres', 'Postres', 'Postres caseros.', 50],
        ] as [$slug, $name, $description, $sortOrder]) {
            $category = (new Category())
                ->setSlug($slug)
                ->setName($name)
                ->setDescription($description)
                ->setSortOrder($sortOrder);

            $manager->persist($category);

            $categories[$slug] = $category;
        }

        $allergens = [];

        foreach ([
            ['gluten', 'Gluten'],
            ['huevo', 'Huevo'],
            ['leche', 'Leche'],
            ['frutos-secos', 'Frutos secos'],
            ['soja', 'Soja'],
            ['sulfitos', 'Sulfitos'],
        ] as [$slug, $name]) {
            $allergen = (new Allergen())
                ->setSlug($slug)
                ->setName($name);

            $manager->persist($allergen);

            $allergens[$slug] = $allergen;
        }

        return [$categories, $allergens];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea productos de ejemplo y los relaciona con categorías y alérgenos.
    // ─────────────────────────────────────────────────────────────────────────

    private function loadProducts(ObjectManager $manager, array $categories, array $allergens): array
    {
        $products = [];

        $rows = [
            ['pollo-asado-entero', 'Pollo asado entero', 'Pollo asado entero listo para llevar.', 'pollos-asados', 12.50, true, '1 unidad', 25, []],
            ['medio-pollo-asado', 'Medio pollo asado', 'Medio pollo asado con su jugo.', 'pollos-asados', 7.00, false, '1/2 unidad', 20, []],
            ['costillar-asado', 'Costillar asado', 'Costillar asado en racion familiar.', 'carnes', 14.90, true, 'racion', 30, []],
            ['patatas-asadas', 'Patatas asadas', 'Racion de patatas asadas.', 'guarniciones', 4.50, true, 'racion', 10, []],
            ['ensaladilla-rusa', 'Ensaladilla rusa', 'Ensaladilla preparada para llevar.', 'guarniciones', 5.50, false, 'racion', 5, ['huevo', 'leche']],
            ['refresco-lata', 'Refresco lata', 'Lata de refresco frio.', 'bebidas', 1.80, false, '330 ml', 1, ['sulfitos']],
            ['tarta-de-queso', 'Tarta de queso', 'Porcion de tarta de queso.', 'postres', 4.00, false, 'porcion', 1, ['gluten', 'huevo', 'leche']],
        ];

        foreach ($rows as [$slug, $name, $description, $categorySlug, $price, $featured, $weight, $prepTime, $allergenSlugs]) {
            $product = (new Product())
                ->setSlug($slug)
                ->setName($name)
                ->setDescription($description)
                ->setCategory($categories[$categorySlug])
                ->setPrice($price)
                ->setFeatured($featured)
                ->setWeight($weight)
                ->setPrepTime($prepTime);

            $product->setAllergens(
                array_map(fn (string $a) => $allergens[$a], $allergenSlugs)
            );

            $manager->persist($product);

            $products[$slug] = $product;
        }

        return $products;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea el local principal y asigna stock inicial a cada producto.
    // ─────────────────────────────────────────────────────────────────────────

    private function loadLocalsAndStock(ObjectManager $manager, array $products): Local
    {
        $local = (new Local())
            ->setName('Asadero BAME')
            ->setAddress('Direccion pendiente de completar')
            ->setCity('Murcia')
            ->setPhone('+34000000000')
            ->setEmail('info@asaderobame.com')
            ->setWhatsapp('+34000000000')
            ->setHours([
                'monday' => ['open' => '09:00', 'close' => '16:00'],
                'tuesday' => ['open' => '09:00', 'close' => '16:00'],
                'wednesday' => ['open' => '09:00', 'close' => '16:00'],
                'thursday' => ['open' => '09:00', 'close' => '16:00'],
                'friday' => ['open' => '09:00', 'close' => '16:00'],
                'saturday' => ['open' => '09:00', 'close' => '16:00'],
                'sunday' => ['open' => '09:00', 'close' => '16:00'],
            ]);

        $manager->persist($local);

        foreach ([
            'pollo-asado-entero' => 20,
            'medio-pollo-asado' => 20,
            'costillar-asado' => 8,
            'patatas-asadas' => 30,
            'ensaladilla-rusa' => 15,
            'refresco-lata' => 80,
            'tarta-de-queso' => 12,
        ] as $slug => $stock) {
            $manager->persist(
                (new LocalProduct())
                    ->setLocal($local)
                    ->setProduct($products[$slug])
                    ->setStock($stock)
            );
        }

        return $local;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea un pedido de ejemplo con varias líneas para probar el flujo completo.
    // ─────────────────────────────────────────────────────────────────────────

    private function loadOrders(ObjectManager $manager, array $users, Local $local, array $products): void
    {
        $order = (new CustomerOrder())
            ->setReference('BAME-DEMO-0001')
            ->setUser($users['cliente'])
            ->setLocal($local)
            ->setStatus(OrderStatus::CONFIRMED)
            ->setType(OrderType::TAKEAWAY)
            ->setPhone('+34600000000')
            ->setNotes('Pedido de prueba para la defensa.');

        $total = 0.0;

        foreach ([
            ['pollo-asado-entero', 1],
            ['patatas-asadas', 2],
            ['refresco-lata', 2],
        ] as [$slug, $quantity]) {
            $product = $products[$slug];

            $line = (new OrderLine())
                ->setProduct($product)
                ->setQuantity($quantity)
                ->setUnitPrice($product->getPrice());

            $order->addLine($line);

            $total += ((float) $product->getPrice()) * $quantity;
        }

        $order->setTotal($total);

        $manager->persist($order);
    }
}