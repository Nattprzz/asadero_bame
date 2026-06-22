<?php

namespace App\Enum;

final class Roles
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const RESPONSABLE = 'ROLE_RESPONSABLE';
    public const GERENTE = 'ROLE_GERENTE';

    public const ALL = [self::USER, self::ADMIN, self::RESPONSABLE, self::GERENTE];
    public const OPERATIONAL = [self::ADMIN, self::GERENTE, self::RESPONSABLE];
}
