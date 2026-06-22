<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductAvailability.php — estados de disponibilidad de productos.
//
// Centraliza los estados que puede tener un producto dentro del catálogo para
// controlar su visibilidad y disponibilidad durante la venta.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Enum;

final class ProductAvailability
{
    public const AVAILABLE = 'available';
    public const LOW_STOCK = 'low_stock';
    public const SOLD_OUT = 'sold_out';
    public const HIDDEN = 'hidden';

    // Lista completa de estados de disponibilidad válidos.
    public const ALL = [
        self::AVAILABLE,
        self::LOW_STOCK,
        self::SOLD_OUT,
        self::HIDDEN,
    ];
}