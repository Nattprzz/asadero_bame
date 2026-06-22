<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderRepository.php — repositorio de pedidos.
//
// Centraliza las consultas relacionadas con los pedidos de la aplicación,
// incluyendo métricas, listados visibles y estadísticas utilizadas por el
// panel de administración.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\CustomerOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CustomerOrder> */
final class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrder::class);
    }

    /** @return CustomerOrder[] */
    public function findAllVisible(): array
    {
        return $this->visibleOrdersQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /** @return CustomerOrder[] */
    public function findVisibleForLocal(int $localId): array
    {
        return $this->visibleOrdersQueryBuilder()
            ->andWhere('local.id = :localId')
            ->setParameter('localId', $localId)
            ->getQuery()
            ->getResult();
    }

    /** @return CustomerOrder[] */
    public function findVisibleForUser(User $user): array
    {
        return $this->visibleOrdersQueryBuilder()
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findForUpdate(int $id): ?CustomerOrder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();
    }

    public function findCheckoutByIdempotencyKey(string $key): ?CustomerOrder
    {
        return $this->findOneBy(['checkoutIdempotencyKey' => $key]);
    }

    public function findForAdmin(?int $localId = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.lines', 'lines')->addSelect('lines')
            ->leftJoin('lines.product', 'product')->addSelect('product')
            ->leftJoin('o.local', 'local')->addSelect('local')
            ->leftJoin('o.user', 'u')->addSelect('u')
            ->orderBy('o.createdAt', 'DESC');

        if ($localId !== null) {
            $qb->andWhere('local.id = :localId')
                ->setParameter('localId', $localId);
        }

        return $qb->getQuery()->getResult();
    }

    // Cuenta los pedidos creados durante el día actual.
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

    // Calcula los ingresos acumulados excluyendo los pedidos cancelados.
    // Puede limitarse a partir de una fecha concreta.
    public function income(?\DateTimeImmutable $from = null): string
    {
        $qb = $this->createQueryBuilder('o')
            ->select('coalesce(sum(o.total), 0)')
            ->andWhere('o.status != :cancelled')
            ->setParameter('cancelled', 'cancelled');

        if ($from !== null) {
            $qb->andWhere('o.createdAt >= :from')
                ->setParameter('from', $from);
        }

        return (string) $qb->getQuery()->getSingleScalarResult();
    }

    // Recupera los pedidos más recientes para paneles y estadísticas.
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

    private function visibleOrdersQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.lines', 'lines')->addSelect('lines')
            ->leftJoin('lines.product', 'product')->addSelect('product')
            ->leftJoin('o.local', 'local')->addSelect('local')
            ->orderBy('o.createdAt', 'DESC');
    }
}
