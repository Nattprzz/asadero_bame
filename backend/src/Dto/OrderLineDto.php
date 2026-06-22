<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderLineDto.php — transformación de entidades OrderLine.
//
// Este DTO convierte una línea de pedido en una estructura de datos sencilla
// preparada para ser enviada como respuesta JSON. Incluye la información del
// producto asociado, cantidades y datos económicos de la línea.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\OrderLine;

final class OrderLineDto
{
    // Convierte una entidad OrderLine en un array asociativo.
    
    public static function fromEntity(OrderLine $line): array
    {
        return [
            'id' => $line->getId(),

            // Identificador del pedido al que pertenece la línea.
            'orderId' => $line->getOrder()?->getId(),

            // Información básica del producto asociado.
            'product' => [
                'id' => $line->getProduct()?->getId(),
                'name' => $line->getProduct()?->getName(),
            ],

            'quantity' => $line->getQuantity(),
            'unitPrice' => (float) $line->getUnitPrice(),
            'subtotal' => (float) $line->getSubtotal(),
        ];
    }
}