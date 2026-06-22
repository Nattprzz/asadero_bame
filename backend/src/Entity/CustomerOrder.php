<?php

// ─────────────────────────────────────────────────────────────────────────────
// CustomerOrder.php — entidad de pedidos.
//
// Representa un pedido realizado por un cliente. Almacena información sobre
// el usuario, estado del pedido, pago, datos de contacto y productos incluidos.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
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

    // Referencia única utilizada para identificar el pedido.
    #[ORM\Column(length: 40, unique: true)]
    private string $reference = '';

    // Cliente que ha realizado el pedido.
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    // Local asociado al pedido.
    #[ORM\ManyToOne(targetEntity: Local::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Local $local = null;

    // Estado actual del pedido.
    #[ORM\Column(length: 40, options: ['default' => OrderStatus::PENDING])]
    private string $status = OrderStatus::PENDING;

    // Tipo de pedido (recogida, envío, etc.).
    #[ORM\Column(length: 40, options: ['default' => OrderType::TAKEAWAY])]
    private string $type = OrderType::TAKEAWAY;

    // Importe total del pedido.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $total = '0.00';

    // Estado actual del pago.
    #[ORM\Column(length: 40, options: ['default' => PaymentStatus::PENDING])]
    private string $paymentStatus = PaymentStatus::PENDING;

    #[ORM\Column(length: 40, options: ['default' => PaymentMethod::CARD])]
    private string $paymentMethod = PaymentMethod::CARD;

    // Identificador de la sesión de pago en Stripe.
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCheckoutSessionId = null;

    // Clave normalizada que hace idempotente la creación de Checkout.
    #[ORM\Column(length: 64, unique: true, nullable: true)]
    private ?string $checkoutIdempotencyKey = null;

    // URL devuelta por Stripe para poder responder igual en reintentos.
    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $stripeCheckoutUrl = null;

    // Fecha en la que se confirmó el pago.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    // Observaciones añadidas por el cliente.
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    // Tiempo estimado de preparación o entrega.
    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $estimatedTime = null;

    // Teléfono de contacto utilizado para el pedido.
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    // Dirección de entrega cuando sea necesaria.
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    // Fecha de creación del pedido.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Líneas que componen el pedido.
    /** @var Collection<int, OrderLine> */
    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderLine::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
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

    public function getReference(): string { return $this->reference; }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getLocal(): ?Local { return $this->local; }

    public function setLocal(?Local $local): self
    {
        $this->local = $local;
        return $this;
    }

    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getType(): string { return $this->type; }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTotal(): string { return $this->total; }

    // Normaliza el total a dos decimales antes de almacenarlo.
    public function setTotal(string|float $total): self
    {
        $this->total = number_format((float) $total, 2, '.', '');
        return $this;
    }

    public function getPaymentStatus(): string { return $this->paymentStatus; }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function getPaymentMethod(): string { return $this->paymentMethod; }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getStripeCheckoutSessionId(): ?string { return $this->stripeCheckoutSessionId; }

    public function setStripeCheckoutSessionId(?string $stripeCheckoutSessionId): self
    {
        $this->stripeCheckoutSessionId = $stripeCheckoutSessionId;
        return $this;
    }

    public function getCheckoutIdempotencyKey(): ?string { return $this->checkoutIdempotencyKey; }

    public function setCheckoutIdempotencyKey(?string $checkoutIdempotencyKey): self
    {
        $this->checkoutIdempotencyKey = $checkoutIdempotencyKey;
        return $this;
    }

    public function getStripeCheckoutUrl(): ?string { return $this->stripeCheckoutUrl; }

    public function setStripeCheckoutUrl(?string $stripeCheckoutUrl): self
    {
        $this->stripeCheckoutUrl = $stripeCheckoutUrl;
        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable { return $this->paidAt; }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getNotes(): ?string { return $this->notes; }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getEstimatedTime(): ?int { return $this->estimatedTime; }

    public function setEstimatedTime(?int $estimatedTime): self
    {
        $this->estimatedTime = $estimatedTime;
        return $this;
    }

    public function getPhone(): ?string { return $this->phone; }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string { return $this->address; }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    // Añade una línea al pedido y mantiene sincronizada la relación.
    public function addLine(OrderLine $line): self
    {
        $this->lines->add($line);
        $line->setOrder($this);

        return $this;
    }
}
