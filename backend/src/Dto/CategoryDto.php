<?php

// ─────────────────────────────────────────────────────────────────────────────
// CategoryDto.php — transformación de entidades Category.
//
// Este DTO convierte una entidad Category en una estructura de datos simple
// preparada para ser enviada como respuesta JSON desde la API.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\Category;

final class CategoryDto
{
    // Convierte una entidad Category en un array asociativo.

    public static function fromEntity(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
        ];
    }
}