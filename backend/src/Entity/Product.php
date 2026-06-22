<?php

// ─────────────────────────────────────────────────────────────────────────────
// Product.php — entidad de productos.
//
// Representa los productos disponibles en el catálogo. Almacena información
// comercial, disponibilidad, precio, alérgenos asociados y relación con los
// distintos locales donde puede estar disponible.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Entity;

use App\Enum\ProductAvailability;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    // Categoría a la que pertenece el producto.
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Category $category = null;

    // Nombre visible del producto.
    #[ORM\Column(length: 180)]
    private string $name = '';

    // Identificador único utilizado en URLs y búsquedas.
    #[ORM\Column(length: 200, unique: true)]
    private string $slug = '';

    // Descripción detallada del producto.
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Precio de venta.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price = '0.00';

    // Indica si el producto está disponible para los clientes.
    #[ORM\Column(options: ['default' => true])]
    private bool $available = true;

    // Estado de disponibilidad dentro del catálogo.
    #[ORM\Column(length: 40, options: ['default' => ProductAvailability::AVAILABLE])]
    private string $availability = ProductAvailability::AVAILABLE;

    // Permite destacar productos en promociones o secciones especiales.
    #[ORM\Column(options: ['default' => false])]
    private bool $featured = false;

    // Peso o formato comercial del producto.
    #[ORM\Column(length: 80, nullable: true)]
    private ?string $weight = null;

    // Tiempo estimado de preparación en minutos.
    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $prepTime = null;

    // Ruta o URL de la imagen principal.
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imagePath = null;

    // Fecha de creación del registro.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Alérgenos asociados al producto.
    /** @var Collection<int, Allergen> */
    #[ORM\ManyToMany(targetEntity: Allergen::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'product_allergen')]
    private Collection $allergens;

    // Relación con el stock disponible en cada local.
    /** @var Collection<int, LocalProduct> */
    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: LocalProduct::class,
        cascade: ['remove']
    )]
    private Collection $localProducts;

    public function __construct()
    {
        $this->allergens = new ArrayCollection();
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

    public function getCategory(): ?Category { return $this->category; }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }

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

    public function getPrice(): string { return $this->price; }

    // Normaliza el precio a dos decimales antes de almacenarlo.
    public function setPrice(string|float $price): self
    {
        $this->price = number_format((float) $price, 2, '.', '');
        return $this;
    }

    public function isAvailable(): bool { return $this->available; }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;
        return $this;
    }

    public function getAvailability(): string { return $this->availability; }

    public function setAvailability(string $availability): self
    {
        $this->availability = $availability;
        return $this;
    }

    public function isFeatured(): bool { return $this->featured; }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;
        return $this;
    }

    public function getWeight(): ?string { return $this->weight; }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getPrepTime(): ?int { return $this->prepTime; }

    public function setPrepTime(?int $prepTime): self
    {
        $this->prepTime = $prepTime;
        return $this;
    }

    public function getImagePath(): ?string { return $this->imagePath; }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /** @return Collection<int, Allergen> */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    // Sustituye completamente la colección de alérgenos asociados.
    public function setAllergens(iterable $allergens): self
    {
        $this->allergens->clear();

        foreach ($allergens as $allergen) {
            $this->allergens->add($allergen);
        }

        return $this;
    }
}