<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalStatus.php — estados posibles de un local.
//
// Centraliza los estados operativos utilizados por los establecimientos de la
// aplicación para mantener valores consistentes en validaciones y consultas.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Enum;

final class LocalStatus
{
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    public const OPEN_SOON = 'open_soon';
    public const CLOSE_SOON = 'close_soon';
    public const TEMPORARILY_CLOSED = 'temporarily_closed';

    // Lista completa de estados válidos.
    public const ALL = [
        self::OPEN,
        self::CLOSED,
        self::OPEN_SOON,
        self::CLOSE_SOON,
        self::TEMPORARILY_CLOSED,
    ];
}