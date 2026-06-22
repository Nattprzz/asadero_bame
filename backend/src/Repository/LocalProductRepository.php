<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalProductRepository.php — repositorio de stock por local.
//
// Gestiona el acceso a los registros que relacionan productos con locales.
// Permite consultar y administrar el stock disponible en cada establecimiento.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\Local;
use App\Entity\LocalProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<LocalProduct> */
final class LocalProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalProduct::class);
    }

    /**
     * Locks stock rows in product-id order to serialize concurrent deductions
     * and reduce the risk of deadlocks between multi-product orders.
     *
     * @param int[] $productIds
     * @return LocalProduct[]
     */
    public function findForUpdate(Local $local, array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        return $this->createQueryBuilder('stock')
            ->innerJoin('stock.product', 'product')->addSelect('product')
            ->andWhere('stock.local = :local')
            ->andWhere('product.id IN (:productIds)')
            ->setParameter('local', $local)
            ->setParameter('productIds', $productIds)
            ->orderBy('product.id', 'ASC')
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getResult();
    }

    /** @return LocalProduct[] */
    public function findByLocalWithProducts(Local $local): array
    {
        return $this->createQueryBuilder('stock')
            ->innerJoin('stock.product', 'product')->addSelect('product')
            ->leftJoin('product.category', 'category')->addSelect('category')
            ->andWhere('stock.local = :local')
            ->setParameter('local', $local)
            ->orderBy('product.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
