<?php

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

    #[ORM\ManyToOne(targetEntity: Local::class, inversedBy: 'localProducts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Local $local = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'localProducts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $stock = 0;

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
    public function getLocal(): ?Local { return $this->local; }
    public function setLocal(Local $local): self { $this->local = $local; return $this; }
    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(Product $product): self { $this->product = $product; return $this; }
    public function getStock(): int { return $this->stock; }
    public function setStock(int $stock): self { $this->stock = $stock; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
