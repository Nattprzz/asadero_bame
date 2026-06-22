<?php

// ─────────────────────────────────────────────────────────────────────────────
// PasswordResetToken.php — entidad de recuperación de contraseña.
//
// Almacena los tokens utilizados durante el proceso de recuperación de acceso.
// Cada token está asociado a un usuario, tiene una fecha de expiración y solo
// puede utilizarse una vez.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordResetTokenRepository::class)]
#[ORM\Table(name: 'password_reset_tokens')]
class PasswordResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Usuario propietario del token.
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    // Hash del token generado para evitar almacenar el valor original.
    #[ORM\Column(length: 64, unique: true)]
    private string $tokenHash = '';

    // Fecha de creación del token.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha límite de validez del token.
    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    // Fecha en la que el token fue utilizado.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $usedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        // Por defecto los tokens expiran tras una hora.
        $this->expiresAt = new \DateTimeImmutable('+1 hour');
    }

    # ─── Getters & Setters ───────────────────────────────────────────────────
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function setTokenHash(string $tokenHash): self
    {
        $this->tokenHash = $tokenHash;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUsedAt(): ?\DateTimeImmutable
    {
        return $this->usedAt;
    }

    // Marca el token como utilizado para impedir reutilizaciones.
    public function markUsed(): self
    {
        $this->usedAt = new \DateTimeImmutable();

        return $this;
    }

    // Comprueba si el token sigue siendo válido.
    public function isValid(): bool
    {
        return $this->usedAt === null
            && $this->expiresAt > new \DateTimeImmutable();
    }
}