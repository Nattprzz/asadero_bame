<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderLineRepository.php — repositorio de líneas de pedido.
//
// Gestiona el acceso a los datos de las líneas de pedido almacenadas en la
// base de datos. Actualmente utiliza únicamente las operaciones estándar
// proporcionadas por Doctrine.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\OrderLine;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<OrderLine> */
final class OrderLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLine::class);
    }

    public function findVisibleFor(User $user, bool $all): array
    {
        $qb = $this->createQueryBuilder('line')
            ->leftJoin('line.order', 'orders')->addSelect('orders')
            ->leftJoin('orders.user', 'user')->addSelect('user')
            ->leftJoin('line.product', 'product')->addSelect('product')
            ->orderBy('line.id', 'DESC');

        if (!$all) {
            $qb->andWhere('orders.user = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneVisibleFor(int $id, User $user, bool $all): ?OrderLine
    {
        $qb = $this->createQueryBuilder('line')
            ->leftJoin('line.order', 'orders')->addSelect('orders')
            ->leftJoin('orders.user', 'user')->addSelect('user')
            ->leftJoin('line.product', 'product')->addSelect('product')
            ->andWhere('line.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        if (!$all) {
            $qb->andWhere('orders.user = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function topProducts(int $limit = 5): array
    {
        return $this->createQueryBuilder('line')
            ->select('product.id AS productId, product.name AS name, SUM(line.quantity) AS quantity')
            ->join('line.product', 'product')
            ->join('line.order', 'orders')
            ->andWhere('orders.status != :cancelled')
            ->setParameter('cancelled', 'cancelled')
            ->groupBy('product.id')
            ->addGroupBy('product.name')
            ->orderBy('quantity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
