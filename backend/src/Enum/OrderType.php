<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderType.php — tipos de pedido.
//
// Define las modalidades de pedido disponibles en la aplicación y centraliza
// los valores válidos utilizados durante su creación y gestión.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Enum;

final class OrderType
{
    public const TAKEAWAY = 'takeaway';
    public const DELIVERY = 'delivery';

    // Lista completa de tipos de pedido válidos.
    public const ALL = [
        self::TAKEAWAY,
        self::DELIVERY,
    ];
}