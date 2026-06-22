<?php

namespace App\Enum;

final class ProductAvailability
{
    public const AVAILABLE = 'available';
    public const LOW_STOCK = 'low_stock';
    public const SOLD_OUT = 'sold_out';
    public const HIDDEN = 'hidden';

    public const ALL = [self::AVAILABLE, self::LOW_STOCK, self::SOLD_OUT, self::HIDDEN];
}
