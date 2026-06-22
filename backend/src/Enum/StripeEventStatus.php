<?php

namespace App\Enum;

final class StripeEventStatus
{
    public const RECEIVED = 'received';
    public const PROCESSED = 'processed';
    public const FAILED = 'failed';

    public const ALL = [
        self::RECEIVED,
        self::PROCESSED,
        self::FAILED,
    ];
}
