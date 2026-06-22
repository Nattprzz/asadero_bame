<?php

// ─────────────────────────────────────────────────────────────────────────────
// PersonalAccessTokenRepository.php — repositorio de tokens de acceso.
//
// Gestiona el acceso a los tokens utilizados para autenticar peticiones en la
// API. Actualmente utiliza las operaciones estándar proporcionadas por Doctrine.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\PersonalAccessToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PersonalAccessToken> */
final class PersonalAccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalAccessToken::class);
    }

    public function revokeAllForUser(User $user): int
    {
        return $this->createQueryBuilder('token')
            ->update()
            ->set('token.revokedAt', ':revokedAt')
            ->where('token.user = :user')
            ->andWhere('token.revokedAt IS NULL')
            ->setParameter('revokedAt', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
