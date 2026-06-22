<?php

namespace App\Entity;

use App\Enum\StripeEventStatus;
use App\Repository\StripeEventLedgerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StripeEventLedgerRepository::class)]
#[ORM\Table(name: 'stripe_event_ledger')]
#[ORM\Index(name: 'idx_stripe_event_ledger_status', columns: ['status'])]
#[ORM\HasLifecycleCallbacks]
class StripeEventLedger
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $stripeEventId = '';

    #[ORM\Column(length: 255)]
    private string $type = '';

    #[ORM\Column(length: 40, options: ['default' => StripeEventStatus::RECEIVED])]
    private string $status = StripeEventStatus::RECEIVED;

    #[ORM\Column(type: 'json')]
    private array $payload = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeEventId(): string
    {
        return $this->stripeEventId;
    }

    public function setStripeEventId(string $stripeEventId): self
    {
        $this->stripeEventId = $stripeEventId;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function isProcessed(): bool
    {
        return $this->status === StripeEventStatus::PROCESSED;
    }
}
