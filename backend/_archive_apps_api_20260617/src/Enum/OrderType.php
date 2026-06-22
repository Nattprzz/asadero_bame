<?php

namespace App\Enum;

final class OrderType
{
    public const TAKEAWAY = 'takeaway';
    public const DELIVERY = 'delivery';

    public const ALL = [self::TAKEAWAY, self::DELIVERY];
}
