<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalRepository.php — repositorio de locales.
//
// Gestiona el acceso a los datos de los establecimientos registrados en la
// aplicación y proporciona consultas específicas para el catálogo público.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\Local;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Local> */
final class LocalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Local::class);
    }

    // Obtiene únicamente los locales activos visibles para los clientes.
    public function findPublic(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.active = true')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}