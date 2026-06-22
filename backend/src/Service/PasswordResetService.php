<?php

// ─────────────────────────────────────────────────────────────────────────────
// PasswordResetService.php — recuperación de contraseña.
//
// Este servicio gestiona la creación de tokens de recuperación y el cambio de
// contraseña del usuario. Los tokens se guardan hasheados y tienen una fecha
// de caducidad para evitar usos indebidos.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Entity\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordResetService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly PasswordResetTokenRepository $tokens,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly int $passwordResetTtlSeconds,
    ) {}

    // Crea un token de recuperación para el email indicado.
    public function createTokenForEmail(string $email): ?string
    {
        $user = $this->users->findOneBy(['email' => mb_strtolower(trim($email))]);

        if (!$user) {
            return null;
        }

        $plainToken = bin2hex(random_bytes(32));

        $reset = (new PasswordResetToken())
            ->setUser($user)
            ->setTokenHash(hash('sha256', $plainToken))
            ->setExpiresAt((new \DateTimeImmutable())->modify('+'.$this->passwordResetTtlSeconds.' seconds'));

        $this->em->persist($reset);
        $this->em->flush();

        return $plainToken;
    }

    // Cambia la contraseña si el token existe, no ha caducado y no se ha usado.
    public function resetPassword(string $plainToken, string $newPassword): bool
    {
        $reset = $this->tokens->findOneBy(['tokenHash' => hash('sha256', $plainToken)]);

        if (!$reset || !$reset->isValid() || !$reset->getUser()) {
            return false;
        }

        $user = $reset->getUser();

        // La nueva contraseña se guarda siempre hasheada.
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword))->touch();

        // El token se marca como usado para evitar reutilizaciones.
        $reset->markUsed();

        $this->em->flush();

        return true;
    }
}