<?php

// ─────────────────────────────────────────────────────────────────────────────
// RegisterRequest.php — DTO para el registro de usuarios.
//
// Define los datos necesarios para crear una nueva cuenta y aplica las
// validaciones básicas antes de procesar el registro.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterRequest
{
    // Nombre del usuario.
    #[Assert\NotBlank]
    public string $name;

    // Apellidos del usuario.
    public string $surname = '';

    // Correo electrónico utilizado para acceder a la cuenta.
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    // Teléfono de contacto opcional.
    public ?string $phone = null;

    // Contraseña de acceso.
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}