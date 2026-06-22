<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderLine.php — entidad de líneas de pedido.
//
// Representa cada producto incluido dentro de un pedido. Almacena la cantidad,
// el precio unitario aplicado en el momento de la compra y observaciones
// específicas de esa línea.
// ─────────────────────────────────────────────────────────────────────────────

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

    // Pedido al que pertenece esta línea.
    #[ORM\ManyToOne(targetEntity: CustomerOrder::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CustomerOrder $order = null;

    // Producto asociado a la línea de pedido.
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Product $product = null;

    // Cantidad solicitada del producto.
    #[ORM\Column]
    private int $quantity = 1;

    // Precio unitario aplicado al realizar el pedido.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitPrice = '0.00';

    // Observaciones opcionales para esta línea.
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

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

    public function getOrder(): ?CustomerOrder { return $this->order; }

    public function setOrder(CustomerOrder $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProduct(): ?Product { return $this->product; }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int { return $this->quantity; }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): string { return $this->unitPrice; }

    // Normaliza el precio a dos decimales antes de almacenarlo.
    public function setUnitPrice(string|float $unitPrice): self
    {
        $this->unitPrice = number_format((float) $unitPrice, 2, '.', '');
        return $this;
    }

    // Calcula automáticamente el subtotal de la línea.
    public function getSubtotal(): string
    {
        return number_format(
            ((float) $this->unitPrice) * $this->quantity,
            2,
            '.',
            ''
        );
    }

    public function getNotes(): ?string { return $this->notes; }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}