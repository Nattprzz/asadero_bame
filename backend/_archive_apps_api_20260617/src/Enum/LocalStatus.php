<?php

namespace App\Enum;

final class LocalStatus
{
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    public const CLOSING_SOON = 'closing_soon';
    public const TEMPORARILY_CLOSED = 'temporarily_closed';

    public const ALL = [self::OPEN, self::CLOSED, self::CLOSING_SOON, self::TEMPORARILY_CLOSED];
}
