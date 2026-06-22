<?php

// ─────────────────────────────────────────────────────────────────────────────
// Category.php — entidad de categorías.
//
// Representa las categorías del catálogo de productos. Permite organizar los
// productos en grupos y controlar su visibilidad y orden de aparición.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Nombre visible de la categoría.
    #[ORM\Column(length: 160)]
    private string $name = '';

    // Identificador único utilizado en URLs y búsquedas.
    #[ORM\Column(length: 180, unique: true)]
    private string $slug = '';

    // Descripción opcional de la categoría.
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Imagen representativa utilizada en el catálogo.
    #[ORM\Column(name: 'image_path', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    // Indica si la categoría está disponible para los clientes.
    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    // Orden de visualización dentro del catálogo.
    #[ORM\Column(options: ['default' => 0])]
    private int $sortOrder = 0;

    // Fecha de creación del registro.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Productos asociados a esta categoría.
    /** @var Collection<int, Product> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Actualiza automáticamente la fecha de modificación al editar la entidad.
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

    public function getImageUrl(): ?string { return $this->imageUrl; }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function isActive(): bool { return $this->active; }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getSortOrder(): int { return $this->sortOrder; }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
