<?php

// ─────────────────────────────────────────────────────────────────────────────
// PasswordResetTokenRepository.php — repositorio de tokens de recuperación.
//
// Gestiona el acceso a los tokens utilizados durante el proceso de recuperación
// de contraseña. Actualmente utiliza las operaciones estándar de Doctrine.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\PasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PasswordResetToken> */
final class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }
}