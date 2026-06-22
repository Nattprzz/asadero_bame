<?php

namespace App\Repository;

use App\Entity\CustomerOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CustomerOrder> */
final class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrder::class);
    }

    public function findVisibleFor(User $user, bool $all): array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.lines', 'lines')->addSelect('lines')
            ->leftJoin('lines.product', 'product')->addSelect('product')
            ->leftJoin('o.local', 'local')->addSelect('local')
            ->orderBy('o.createdAt', 'DESC');

        if (!$all) {
            $qb->andWhere('o.user = :user')->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function countToday(): int
    {
        $start = new \DateTimeImmutable('today');
        return (int) $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->andWhere('o.createdAt >= :start')
            ->setParameter('start', $start)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function income(?\DateTimeImmutable $from = null): string
    {
        $qb = $this->createQueryBuilder('o')
            ->select('coalesce(sum(o.total), 0)')
            ->andWhere('o.status != :cancelled')
            ->setParameter('cancelled', 'cancelled');

        if ($from !== null) {
            $qb->andWhere('o.createdAt >= :from')->setParameter('from', $from);
        }

        return (string) $qb->getQuery()->getSingleScalarResult();
    }

    public function recent(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')->addSelect('u')
            ->leftJoin('o.lines', 'lines')->addSelect('lines')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
