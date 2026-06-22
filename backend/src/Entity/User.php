<?php

// ─────────────────────────────────────────────────────────────────────────────
// User.php — entidad de usuarios.
//
// Representa a los usuarios registrados en la aplicación. Almacena sus datos
// personales, credenciales de acceso y roles dentro del sistema.
// ─────────────────────────────────────────────────────────────────────────────

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

    // Nombre del usuario.
    #[ORM\Column(length: 120)]
    private string $name = '';

    // Apellidos del usuario.
    #[ORM\Column(length: 120, options: ['default' => ''])]
    private string $surname = '';

    // Correo electrónico utilizado para acceder a la aplicación.
    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    // Nombre de usuario opcional.
    #[ORM\Column(length: 80, unique: true, nullable: true)]
    private ?string $username = null;

    // Teléfono de contacto.
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    #[ORM\ManyToOne(targetEntity: Local::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Local $local = null;

    // Roles asignados al usuario.
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    // Contraseña cifrada.
    #[ORM\Column]
    private string $password = '';

    // Fecha de creación del usuario.
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    // Fecha de última modificación.
    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    // Pedidos realizados por el usuario.
    /** @var Collection<int, CustomerOrder> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CustomerOrder::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getSurname(): string { return $this->surname; }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): string { return $this->email; }

    // Guarda el email en minúsculas para evitar duplicados por formato.
    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower($email);
        return $this;
    }

    public function getUsername(): ?string { return $this->username; }

    // Guarda el nombre de usuario en minúsculas y permite valores nulos.
    public function setUsername(?string $username): self
    {
        $this->username = $username === null || $username === ''
            ? null
            : mb_strtolower($username);

        return $this;
    }

    public function getPhone(): ?string { return $this->phone; }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getLocal(): ?Local { return $this->local; }

    public function setLocal(?Local $local): self
    {
        $this->local = $local;
        return $this;
    }

    // Garantiza que ROLE_USER siempre esté presente.
    public function getRoles(): array
    {
        return array_values(
            array_unique([
                ...$this->roles,
                'ROLE_USER',
            ])
        );
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function getPassword(): string { return $this->password; }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    // Identificador utilizado por Symfony durante la autenticación.
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // No se almacenan credenciales temporales en memoria.
    public function eraseCredentials(): void {}
}
