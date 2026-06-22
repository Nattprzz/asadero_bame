<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\HasLifecycleCallbacks]
class CustomerOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 40, unique: true)]
    private string $reference = '';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Local::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Local $local = null;

    #[ORM\Column(length: 40, options: ['default' => OrderStatus::PENDING])]
    private string $status = OrderStatus::PENDING;

    #[ORM\Column(length: 40, options: ['default' => OrderType::TAKEAWAY])]
    private string $type = OrderType::TAKEAWAY;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $total = '0.00';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $estimatedTime = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, OrderLine> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getLocal(): ?Local { return $this->local; }
    public function setLocal(?Local $local): self { $this->local = $local; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getTotal(): string { return $this->total; }
    public function setTotal(string|float $total): self { $this->total = number_format((float) $total, 2, '.', ''); return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }
    public function getEstimatedTime(): ?int { return $this->estimatedTime; }
    public function setEstimatedTime(?int $estimatedTime): self { $this->estimatedTime = $estimatedTime; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }
    public function addLine(OrderLine $line): self { $this->lines->add($line); $line->setOrder($this); return $this; }
}
