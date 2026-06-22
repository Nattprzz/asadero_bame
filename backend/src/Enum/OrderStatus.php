<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderStatus.php — estados posibles de un pedido.
//
// Centraliza los estados utilizados durante el ciclo de vida de un pedido y
// define las transiciones permitidas entre ellos.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Enum;

final class OrderStatus
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const PREPARING = 'preparing';
    public const READY = 'ready';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    // Lista completa de estados válidos.
    public const ALL = [
        self::PENDING,
        self::CONFIRMED,
        self::PREPARING,
        self::READY,
        self::COMPLETED,
        self::CANCELLED,
    ];

    // Flujo permitido de cambios de estado.
    public const TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::PREPARING, self::CANCELLED],
        self::PREPARING => [self::READY],
        self::READY => [self::COMPLETED],
        self::COMPLETED => [],
        self::CANCELLED => [],
    ];
}