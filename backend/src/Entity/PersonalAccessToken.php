<?php

// ─────────────────────────────────────────────────────────────────────────────
// PersonalAccessToken.php — tokens de acceso personales.
//
// Almacena los tokens utilizados para autenticar peticiones a la API.
// Cada token pertenece a un usuario y puede tener fecha de expiración o ser
// revocado manualmente.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Repository\PersonalAccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalAccessTokenRepository::class)]
#[ORM\Table(name: 'personal_access_tokens')]
class PersonalAccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Usuario propietario del token.
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    // Hash del token para evitar almacenar el valor original.
    #[ORM\Column(length: 64, unique: true)]
    private string $tokenHash = '';

    // Fecha de creación del token.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de expiración opcional.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    // Fecha en la que el token fue revocado.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    // Revoca el token para impedir futuros accesos.
    public function revoke(): self
    {
        $this->revokedAt = new \DateTimeImmutable();

        return $this;
    }

    // Comprueba si el token sigue siendo válido.
    public function isValid(): bool
    {
        return $this->revokedAt === null
            && ($this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable());
    }
}