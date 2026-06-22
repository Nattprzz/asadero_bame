<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalDto.php — transformación de entidades Local.
//
// Este DTO convierte una entidad Local en una estructura de datos sencilla
// preparada para ser enviada como respuesta JSON desde la API.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\Local;

final class LocalDto
{
    // Convierte una entidad Local en un array asociativo.

    public static function fromEntity(Local $local): array
    {
        return [
            'id' => $local->getId(),
            'name' => $local->getName(),
            'address' => $local->getAddress(),
            'phone' => $local->getPhone(),
            'openingHours' => $local->getOpeningHours(),
        ];
    }
}