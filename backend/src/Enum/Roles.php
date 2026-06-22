<?php

// ─────────────────────────────────────────────────────────────────────────────
// Roles.php — roles de usuario del sistema.
//
// Centraliza los roles disponibles dentro de la aplicación y permite agrupar
// aquellos que tienen permisos operativos sobre la gestión del negocio.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Enum;

final class Roles
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const RESPONSABLE = 'ROLE_RESPONSABLE';
    public const GERENTE = 'ROLE_GERENTE';

    // Lista completa de roles válidos.
    public const ALL = [
        self::USER,
        self::ADMIN,
        self::RESPONSABLE,
        self::GERENTE,
    ];

    // Roles con permisos operativos sobre pedidos y gestión interna.
    public const OPERATIONAL = [
        self::ADMIN,
        self::GERENTE,
        self::RESPONSABLE,
    ];
}