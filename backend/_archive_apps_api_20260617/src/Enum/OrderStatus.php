<?php

namespace App\Enum;

final class OrderStatus
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const PREPARING = 'preparing';
    public const READY = 'ready';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const ALL = [
        self::PENDING,
        self::CONFIRMED,
        self::PREPARING,
        self::READY,
        self::COMPLETED,
        self::CANCELLED,
    ];

    public const TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::PREPARING, self::CANCELLED],
        self::PREPARING => [self::READY],
        self::READY => [self::COMPLETED],
        self::COMPLETED => [],
        self::CANCELLED => [],
    ];
}
