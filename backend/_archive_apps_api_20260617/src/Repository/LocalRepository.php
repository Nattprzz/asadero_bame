<?php

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

    public function findPublic(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.active = true')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
