<?php

// ─────────────────────────────────────────────────────────────────────────────
// CategoryService.php — gestión de categorías.
//
// Centraliza la lógica de negocio relacionada con la creación, modificación
// y eliminación de categorías del catálogo.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Slugger $slugger,
    ) {}

    // Crea una nueva categoría a partir de los datos recibidos.
    public function create(array $payload): Category
    {
        $category = new Category();

        $this->apply($category, $payload);

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    // Actualiza los datos de una categoría existente.
    public function update(Category $category, array $payload): Category
    {
        $this->apply($category, $payload);

        $this->em->flush();

        return $category;
    }

    // Elimina una categoría de la base de datos.
    public function delete(Category $category): void
    {
        $this->em->remove($category);
        $this->em->flush();
    }

    // Aplica los cambios recibidos sobre la entidad.
    private function apply(Category $category, array $payload): void
    {
        // Actualiza el nombre y genera automáticamente el slug si no se envía.
        if (isset($payload['name'])) {
            $category->setName((string) $payload['name']);

            if (empty($payload['slug'])) {
                $category->setSlug(
                    $this->slugger->slug((string) $payload['name'])
                );
            }
        }

        // Si se proporciona un slug, se normaliza antes de guardarlo.
        if (isset($payload['slug'])) {
            $category->setSlug(
                $this->slugger->slug((string) $payload['slug'])
            );
        }

        // Permite actualizar la descripción, incluso estableciéndola a null.
        if (array_key_exists('description', $payload)) {
            $category->setDescription($payload['description']);
        }
    }
}