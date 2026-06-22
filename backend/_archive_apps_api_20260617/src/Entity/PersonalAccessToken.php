<?php

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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $tokenHash = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getTokenHash(): string { return $this->tokenHash; }
    public function setTokenHash(string $tokenHash): self { $this->tokenHash = $tokenHash; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getExpiresAt(): ?\DateTimeImmutable { return $this->expiresAt; }
    public function setExpiresAt(?\DateTimeImmutable $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }
    public function getRevokedAt(): ?\DateTimeImmutable { return $this->revokedAt; }
    public function revoke(): self { $this->revokedAt = new \DateTimeImmutable(); return $this; }
    public function isValid(): bool { return $this->revokedAt === null && ($this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable()); }
}
