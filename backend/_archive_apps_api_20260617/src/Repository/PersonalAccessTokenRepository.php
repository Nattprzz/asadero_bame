<?php

namespace App\Repository;

use App\Entity\PersonalAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PersonalAccessToken> */
final class PersonalAccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalAccessToken::class);
    }
}
