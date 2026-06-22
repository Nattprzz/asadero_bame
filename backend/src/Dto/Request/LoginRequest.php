<?php

// ─────────────────────────────────────────────────────────────────────────────
// LoginRequest.php — DTO para el inicio de sesión.
//
// Define los datos necesarios para autenticar a un usuario y aplica las
// validaciones básicas antes de procesar la solicitud.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class LoginRequest
{
    // Correo electrónico utilizado para acceder a la cuenta.
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    // Contraseña asociada al usuario.
    #[Assert\NotBlank]
    public string $password;
}