<?php

// ─────────────────────────────────────────────────────────────────────────────
// PayloadValidator.php — validación básica de datos recibidos.
//
// Este servicio proporciona validaciones sencillas para comprobar que los
// campos obligatorios estén presentes y que las contraseñas cumplan unos
// requisitos mínimos de seguridad.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

final class PayloadValidator
{
    // Comprueba que todos los campos requeridos estén presentes.
    public function require(array $payload, array $fields): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $payload) || $payload[$field] === null || $payload[$field] === '') {
                $errors[$field][] = 'This field is required.';
            }
        }

        return $errors;
    }

    // Valida los requisitos mínimos de seguridad de una contraseña.
    public function passwordErrors(string $password): array
    {
        $errors = [];

        if (mb_strlen($password) < 8) {
            $errors[] = 'Password must contain at least 8 characters.';
        }

        // Comprueba que exista al menos una mayúscula, una minúscula y un número.
        if (
            !preg_match('/[A-Z]/', $password)
            || !preg_match('/[a-z]/', $password)
            || !preg_match('/\d/', $password)
        ) {
            $errors[] = 'Password must contain uppercase, lowercase and number.';
        }

        return $errors;
    }
}