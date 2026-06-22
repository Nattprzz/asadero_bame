<?php

// ─────────────────────────────────────────────────────────────────────────────
// UserRepository.php — repositorio de usuarios.
//
// Gestiona el acceso a los datos de los usuarios registrados en la aplicación.
// Actualmente utiliza las operaciones estándar proporcionadas por Doctrine.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<User> */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}