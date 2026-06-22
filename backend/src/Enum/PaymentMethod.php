<?php

namespace App\Enum;

final class PaymentMethod
{
    public const CARD = 'card';
    public const STRIPE = 'stripe';
    public const PAY_AT_STORE = 'pay_at_store';

    public const ALL = [
        self::CARD,
        self::STRIPE,
        self::PAY_AT_STORE,
    ];

    public const ONLINE = [
        self::CARD,
        self::STRIPE,
    ];

    public static function normalize(string $method): string
    {
        return mb_strtolower(trim($method));
    }

    public static function requiresOnlinePayment(string $method): bool
    {
        return in_array($method, self::ONLINE, true);
    }
}
