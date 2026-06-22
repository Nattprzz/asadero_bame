<?php

// ─────────────────────────────────────────────────────────────────────────────
// AllergenService.php — gestión de alérgenos.
//
// Este servicio centraliza las operaciones de creación, actualización y
// eliminación de alérgenos. También genera automáticamente el slug cuando
// no se proporciona uno de forma manual.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\Allergen;
use Doctrine\ORM\EntityManagerInterface;

final class AllergenService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Slugger $slugger
    ) {}

    // Crea un nuevo alérgeno y lo guarda en la base de datos.
    public function create(array $payload): Allergen
    {
        $allergen = new Allergen();

        $this->apply($allergen, $payload);

        $this->em->persist($allergen);
        $this->em->flush();

        return $allergen;
    }

    // Actualiza los datos de un alérgeno existente.
    public function update(Allergen $allergen, array $payload): Allergen
    {
        $this->apply($allergen, $payload);

        $this->em->flush();

        return $allergen;
    }

    // Elimina un alérgeno de la base de datos.
    public function delete(Allergen $allergen): void
    {
        $this->em->remove($allergen);
        $this->em->flush();
    }

    // Aplica los datos recibidos a la entidad.
    private function apply(Allergen $allergen, array $payload): void
    {
        if (isset($payload['name'])) {
            $allergen->setName((string) $payload['name']);

            // Si no se indica un slug, se genera a partir del nombre.
            if (empty($payload['slug'])) {
                $allergen->setSlug(
                    $this->slugger->slug((string) $payload['name'])
                );
            }
        }

        if (isset($payload['slug'])) {
            $allergen->setSlug(
                $this->slugger->slug((string) $payload['slug'])
            );
        }
    }
}