<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name = '';

    #[ORM\Column(length: 120, options: ['default' => ''])]
    private string $surname = '';

    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(length: 80, unique: true, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, CustomerOrder> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CustomerOrder::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getSurname(): string { return $this->surname; }
    public function setSurname(string $surname): self { $this->surname = $surname; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = mb_strtolower($email); return $this; }
    public function getUsername(): ?string { return $this->username; }
    public function setUsername(?string $username): self { $this->username = $username === null || $username === '' ? null : mb_strtolower($username); return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
    public function getRoles(): array { return array_values(array_unique([...$this->roles, 'ROLE_USER'])); }
    public function setRoles(array $roles): self { $this->roles = array_values(array_unique($roles)); return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getUserIdentifier(): string { return $this->email; }
    public function eraseCredentials(): void {}
}
