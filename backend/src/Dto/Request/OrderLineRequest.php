<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderLineRequest.php — DTO para las líneas de pedido.
//
// Representa cada producto incluido en un pedido junto con su cantidad y
// posibles observaciones adicionales.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderLineRequest
{
    // Identificador del producto seleccionado.
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $productId;

    // Cantidad solicitada del producto.
    #[Assert\Positive]
    public int $quantity = 1;

    // Observaciones opcionales para esta línea del pedido.
    public ?string $notes = null;
}