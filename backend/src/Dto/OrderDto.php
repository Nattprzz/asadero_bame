<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderDto.php — transformación de entidades CustomerOrder.
//
// Este DTO convierte un pedido en una estructura de datos preparada para ser
// enviada como respuesta JSON. Además de la información principal del pedido,
// incluye los datos del local, usuario y líneas asociadas.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\CustomerOrder;

final class OrderDto
{
    // Convierte una entidad CustomerOrder en un array asociativo.
    
    public static function fromEntity(CustomerOrder $order): array
    {
        return [
            'id' => $order->getId(),
            'reference' => $order->getReference(),
            'status' => $order->getStatus(),
            'type' => $order->getType(),
            'total' => (float) $order->getTotal(),
            'notes' => $order->getNotes(),

            // Fechas formateadas siguiendo el estándar ISO 8601.
            'createdAt' => $order->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $order->getUpdatedAt()->format(DATE_ATOM),

            // Información relacionada con el local y el usuario.
            'local' => $order->getLocal() ? LocalDto::fromEntity($order->getLocal()) : null,
            'user' => $order->getUser() ? UserDto::fromEntity($order->getUser()) : null,

            // Productos incluidos en el pedido.
            'lines' => array_map(static fn ($line) => [
                'id' => $line->getId(),
                'product' => [
                    'id' => $line->getProduct()?->getId(),
                    'name' => $line->getProduct()?->getName(),
                ],
                'quantity' => $line->getQuantity(),
                'unitPrice' => (float) $line->getUnitPrice(),
                'subtotal' => (float) $line->getSubtotal(),
            ], $order->getLines()->toArray()),
        ];
    }
}