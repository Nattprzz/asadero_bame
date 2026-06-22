<?php

namespace App\Repository;

use App\Entity\StripeEventLedger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<StripeEventLedger> */
final class StripeEventLedgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripeEventLedger::class);
    }
}
