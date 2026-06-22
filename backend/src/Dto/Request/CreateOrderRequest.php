<?php

// ─────────────────────────────────────────────────────────────────────────────
// CreateOrderRequest.php — DTO para la creación de pedidos.
//
// Define la estructura y validaciones de los datos necesarios para registrar
// un nuevo pedido en la aplicación.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto\Request;

use App\Enum\OrderType;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrderRequest
{
    // Tipo de pedido (recogida, envío, etc.).
    #[Assert\NotBlank]
    #[Assert\Choice(OrderType::ALL)]
    public string $type;

    // Datos opcionales asociados al pedido.
    public ?int $localId = null;
    public ?string $notes = null;
    public ?string $phone = null;
    public ?string $address = null;

    // El pedido debe contener al menos una línea de producto.
    /** @var list<OrderLineRequest> */
    #[Assert\Count(min: 1)]
    public array $lines = [];
}