<?php

// ─────────────────────────────────────────────────────────────────────────────
// Allergen.php — entidad de alérgenos.
//
// Representa los alérgenos que pueden estar presentes en los productos del
// catálogo. Permite almacenar información descriptiva y mantener la relación
// con los productos que los contienen.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Repository\AllergenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllergenRepository::class)]
#[ORM\Table(name: 'allergens')]
#[ORM\HasLifecycleCallbacks]
class Allergen
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Nombre visible del alérgeno.
    #[ORM\Column(length: 160)]
    private string $name = '';

    // Identificador único utilizado en URLs y búsquedas.
    #[ORM\Column(length: 180, unique: true)]
    private string $slug = '';

    // Descripción opcional del alérgeno.
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Icono representativo utilizado en el frontend.
    #[ORM\Column(name: 'icon_name', length: 500)]
    private ?string $iconUrl = null;

    // Fecha de creación del registro.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Productos asociados a este alérgeno.
    /** @var Collection<int, Product> */
    #[ORM\ManyToMany(mappedBy: 'allergens', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Actualiza automáticamente la fecha de modificación antes de guardar cambios.
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

    public function getSlug(): string { return $this->slug; }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getIconUrl(): ?string { return $this->iconUrl; }

    public function setIconUrl(?string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
