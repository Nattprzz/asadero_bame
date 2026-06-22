<?php

// ─────────────────────────────────────────────────────────────────────────────
// AllergenDto.php — transformación de entidades Allergen.
//
// Este DTO se encarga de convertir una entidad Allergen en una estructura
// de datos sencilla preparada para ser serializada como respuesta JSON
// dentro de la API.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\Allergen;

final class AllergenDto
{
    // Convierte una entidad Allergen en un array asociativo.
    public static function fromEntity(Allergen $allergen): array
    {
        return [
            // Identificador único del alérgeno.
            'id' => $allergen->getId(),

            // Nombre legible mostrado al usuario.
            'name' => $allergen->getName(),

            // Versión amigable para URLs.
            'slug' => $allergen->getSlug(),
        ];
    }
}