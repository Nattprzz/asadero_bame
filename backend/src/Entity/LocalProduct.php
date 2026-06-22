<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalProduct.php — entidad de stock por local.
//
// Relaciona cada producto con un local concreto y permite controlar el stock
// disponible de ese producto en cada establecimiento.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Repository\LocalProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalProductRepository::class)]
#[ORM\Table(name: 'local_product')]
#[ORM\UniqueConstraint(name: 'uniq_local_product', columns: ['local_id', 'product_id'])]
#[ORM\HasLifecycleCallbacks]
class LocalProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Local al que pertenece el stock.
    #[ORM\ManyToOne(targetEntity: Local::class, inversedBy: 'localProducts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Local $local = null;

    // Producto asociado al local.
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'localProducts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    // Cantidad disponible del producto en este local.
    #[ORM\Column(options: ['default' => 0])]
    private int $stock = 0;

    #[ORM\Column(options: ['default' => true])]
    private bool $available = true;

    // Fecha de creación del registro.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Actualiza automáticamente la fecha de modificación.
    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    # ─── Getters & Setters ───────────────────────────────────────────────────
    
    public function getId(): ?int { return $this->id; }

    public function getLocal(): ?Local { return $this->local; }

    public function setLocal(Local $local): self
    {
        $this->local = $local;
        return $this;
    }

    public function getProduct(): ?Product { return $this->product; }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getStock(): int { return $this->stock; }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function isAvailable(): bool { return $this->available; }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
