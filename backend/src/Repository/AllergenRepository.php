<?php

// ─────────────────────────────────────────────────────────────────────────────
// AllergenRepository.php — repositorio de alérgenos.
//
// Proporciona el acceso a los datos de la entidad Allergen utilizando Doctrine.
// Actualmente utiliza los métodos estándar heredados del repositorio base.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\Allergen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Allergen> */
final class AllergenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Allergen::class);
    }
}