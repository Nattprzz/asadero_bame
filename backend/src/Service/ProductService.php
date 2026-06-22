<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductService.php — gestión de productos.
//
// Este servicio centraliza las operaciones de búsqueda, creación,
// actualización y eliminación de productos. También se encarga de gestionar
// las relaciones con categorías y alérgenos.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\Product;
use App\Repository\AllergenRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly AllergenRepository $allergens,
        private readonly Slugger $slugger,
    ) {}

    // Busca productos aplicando los filtros recibidos.
    public function search(array $filters): array
    {
        return $this->products->search($filters);
    }

    // Crea un nuevo producto y lo guarda en la base de datos.
    public function create(array $payload): Product
    {
        $product = new Product();

        $this->apply($product, $payload);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    // Actualiza un producto existente.
    public function update(Product $product, array $payload): Product
    {
        $this->apply($product, $payload);

        $this->em->flush();

        return $product;
    }

    // Elimina un producto de la base de datos.
    public function delete(Product $product): void
    {
        $this->em->remove($product);
        $this->em->flush();
    }

    // Aplica los datos recibidos a la entidad.
    private function apply(Product $product, array $payload): void
    {
        if (array_key_exists('name', $payload)) {
            $product->setName((string) $payload['name']);

            // Si no se proporciona un slug, se genera a partir del nombre.
            if (!array_key_exists('slug', $payload) || !$payload['slug']) {
                $product->setSlug(
                    $this->slugger->slug((string) $payload['name'])
                );
            }
        }

        if (array_key_exists('slug', $payload) && $payload['slug']) {
            $product->setSlug(
                $this->slugger->slug((string) $payload['slug'])
            );
        }

        if (array_key_exists('description', $payload)) {
            $product->setDescription($payload['description']);
        }

        if (array_key_exists('price', $payload)) {
            $product->setPrice($payload['price']);
        }

        if (array_key_exists('available', $payload)) {
            $product->setAvailable((bool) $payload['available']);
        }

        if (array_key_exists('imagePath', $payload)) {
            $product->setImagePath($payload['imagePath']);
        }

        // Asocia el producto a la categoría seleccionada.
        if (array_key_exists('categoryId', $payload)) {
            $category = $this->categories->find((int) $payload['categoryId']);

            if (!$category) {
                throw new \InvalidArgumentException('Category not found.');
            }

            $product->setCategory($category);
        }

        // Actualiza la lista de alérgenos asociados al producto.
        if (array_key_exists('allergenIds', $payload)) {
            $product->clearAllergens();

            foreach ((array) $payload['allergenIds'] as $id) {
                $allergen = $this->allergens->find((int) $id);

                if ($allergen) {
                    $product->addAllergen($allergen);
                }
            }
        }

        $product->touch();
    }
}