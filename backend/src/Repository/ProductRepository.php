<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductRepository.php — repositorio de productos.
//
// Gestiona las consultas relacionadas con el catálogo de productos y permite
// aplicar filtros dinámicos para mostrar únicamente los productos visibles
// para los clientes.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Product> */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // Obtiene los productos públicos aplicando los filtros recibidos.
    public function searchPublic(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.allergens', 'a')->addSelect('a')

            // Solo se muestran productos disponibles y visibles.
            ->andWhere('p.available = true')
            ->andWhere('p.availability != :hidden')
            ->setParameter('hidden', 'hidden');

        // Filtrado por categoría (id o slug).
        if (!empty($filters['category'])) {
            is_numeric($filters['category'])
                ? $qb->andWhere('c.id = :category')
                    ->setParameter('category', (int) $filters['category'])
                : $qb->andWhere('c.slug = :category')
                    ->setParameter('category', $filters['category']);
        }

        // Filtrado por estado de disponibilidad.
        if (!empty($filters['availability'])) {
            $qb->andWhere('p.availability = :availability')
                ->setParameter('availability', $filters['availability']);
        }

        // Filtrado de productos destacados.
        if (isset($filters['featured'])) {
            $qb->andWhere('p.featured = :featured')
                ->setParameter('featured', (bool) $filters['featured']);
        }

        // Búsqueda por nombre.
        if (!empty($filters['search'])) {
            $qb->andWhere('lower(p.name) like :search')
                ->setParameter(
                    'search',
                    '%' . mb_strtolower($filters['search']) . '%'
                );
        }

        // Filtrado por alérgenos.
        if (!empty($filters['allergens'])) {
            $allergens = array_filter(
                array_map(
                    'trim',
                    explode(',', (string) $filters['allergens'])
                )
            );

            $qb->andWhere('a.slug in (:allergens)')
                ->setParameter('allergens', $allergens);
        }

        // Precio mínimo.
        if (isset($filters['minPrice'])) {
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', (float) $filters['minPrice']);
        }

        // Precio máximo.
        if (isset($filters['maxPrice'])) {
            $qb->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', (float) $filters['maxPrice']);
        }

        return $qb->orderBy('p.featured', 'DESC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findForAdmin(?int $localId = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.allergens', 'a')->addSelect('a')
            ->orderBy('p.name', 'ASC');

        if ($localId !== null) {
            $qb->innerJoin('p.localProducts', 'stock')
                ->innerJoin('stock.local', 'local')
                ->andWhere('local.id = :localId')
                ->setParameter('localId', $localId);
        }

        return $qb->getQuery()->getResult();
    }

    public function belongsToLocal(Product $product, int $localId): bool
    {
        return (int) $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.localProducts', 'stock')
            ->innerJoin('stock.local', 'local')
            ->andWhere('p = :product')
            ->andWhere('local.id = :localId')
            ->setParameter('product', $product)
            ->setParameter('localId', $localId)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
