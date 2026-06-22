<?php

// ─────────────────────────────────────────────────────────────────────────────
// Local.php — entidad de locales.
//
// Representa los establecimientos físicos del negocio. Almacena información
// de contacto, ubicación, horarios y estado operativo de cada local.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Enum\LocalStatus;
use App\Repository\LocalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\Table(name: 'locals')]
#[ORM\HasLifecycleCallbacks]
class Local
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Nombre comercial del local.
    #[ORM\Column(length: 180)]
    private string $name = '';

    // Dirección física del establecimiento.
    #[ORM\Column(length: 255)]
    private string $address = '';

    // Ciudad donde se encuentra el local.
    #[ORM\Column(length: 120, options: ['default' => 'Murcia'])]
    private string $city = 'Murcia';

    // Código postal del local.
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    // Teléfono principal de contacto.
    #[ORM\Column(length: 40)]
    private string $phone = '';

    // Correo electrónico de contacto.
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    // Coordenada de latitud para geolocalización.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    // Coordenada de longitud para geolocalización.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    // Horario semanal almacenado en formato JSON.
    #[ORM\Column(type: 'json')]
    private array $hours = [];

    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    private array $reservationHours = [];

    // Indica si el local está disponible para los clientes.
    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    // Estado operativo actual del local.
    #[ORM\Column(length: 40, options: ['default' => LocalStatus::OPEN])]
    private string $status = LocalStatus::OPEN;

    // Número de WhatsApp para contacto rápido.
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $whatsapp = null;

    // Fecha de creación del registro.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Relación con los productos disponibles en este local.
    /** @var Collection<int, LocalProduct> */
    #[ORM\OneToMany(
        mappedBy: 'local',
        targetEntity: LocalProduct::class,
        cascade: ['remove']
    )]
    private Collection $localProducts;

    public function __construct()
    {
        $this->localProducts = new ArrayCollection();
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

    public function getName(): string { return $this->name; }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): string { return $this->address; }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): string { return $this->city; }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): ?string { return $this->postalCode; }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getPhone(): string { return $this->phone; }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getLatitude(): ?string { return $this->latitude; }

    // Convierte automáticamente la latitud a string para mantener consistencia.
    public function setLatitude(string|float|null $latitude): self
    {
        $this->latitude = $latitude === null ? null : (string) $latitude;
        return $this;
    }

    public function getLongitude(): ?string { return $this->longitude; }

    // Convierte automáticamente la longitud a string para mantener consistencia.
    public function setLongitude(string|float|null $longitude): self
    {
        $this->longitude = $longitude === null ? null : (string) $longitude;
        return $this;
    }

    public function getHours(): array { return $this->hours; }

    public function setHours(array $hours): self
    {
        $this->hours = $hours;
        return $this;
    }

    public function getReservationHours(): array { return $this->reservationHours; }

    public function setReservationHours(array $reservationHours): self
    {
        $this->reservationHours = $reservationHours;
        return $this;
    }

    public function isActive(): bool { return $this->active; }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getWhatsapp(): ?string { return $this->whatsapp; }

    public function setWhatsapp(?string $whatsapp): self
    {
        $this->whatsapp = $whatsapp;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
