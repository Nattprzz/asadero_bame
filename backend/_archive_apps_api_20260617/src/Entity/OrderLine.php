<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
#[ORM\Table(name: 'order_lines')]
#[ORM\HasLifecycleCallbacks]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CustomerOrder::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CustomerOrder $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Product $product = null;

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitPrice = '0.00';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

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
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): ?CustomerOrder { return $this->order; }
    public function setOrder(CustomerOrder $order): self { $this->order = $order; return $this; }
    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(Product $product): self { $this->product = $product; return $this; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = $quantity; return $this; }
    public function getUnitPrice(): string { return $this->unitPrice; }
    public function setUnitPrice(string|float $unitPrice): self { $this->unitPrice = number_format((float) $unitPrice, 2, '.', ''); return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
