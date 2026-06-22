<?php

// ─────────────────────────────────────────────────────────────────────────────
// ApiEnvelope.php — estructura estándar de respuesta de la API.
//
// Se utiliza para encapsular los datos devueltos por la API junto con el
// código de estado HTTP asociado a la respuesta.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto\Response;

final readonly class ApiEnvelope
{
    public function __construct(
        // Información devuelta al cliente.
        public mixed $data,

        // Código de estado HTTP de la respuesta.
        public int $status,
    ) {}
}