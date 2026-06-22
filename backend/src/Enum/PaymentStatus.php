<?php

namespace App\Enum;

final class PaymentStatus
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const FAILED = 'failed';

    public const ALL = [
        self::PENDING,
        self::PAID,
        self::FAILED,
    ];
}
