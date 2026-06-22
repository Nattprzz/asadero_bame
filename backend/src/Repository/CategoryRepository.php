<?php

// ─────────────────────────────────────────────────────────────────────────────
// CategoryRepository.php — repositorio de categorías.
//
// Gestiona el acceso a los datos de las categorías y proporciona consultas
// personalizadas relacionadas con el catálogo público.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Category> */
final class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // Obtiene únicamente las categorías activas visibles para los clientes.
    public function findPublic(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.active = true')
            ->orderBy('c.sortOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}