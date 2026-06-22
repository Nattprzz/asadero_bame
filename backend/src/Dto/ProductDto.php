<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductDto.php — transformación de entidades Product.
//
// Este DTO convierte un producto en una estructura de datos preparada para
// ser enviada como respuesta JSON. Además de la información principal,
// incluye la categoría asociada, los alérgenos y la URL de la imagen.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Dto;

use App\Entity\Product;
use App\Service\ImageUrlResolver;

final class ProductDto
{
    // Convierte una entidad Product en un array asociativo.
    
    public static function fromEntity(Product $product, ImageUrlResolver $images): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'description' => $product->getDescription(),
            'price' => (float) $product->getPrice(),
            'available' => $product->isAvailable(),

            // Genera la URL pública de la imagen del producto.
            'imageUrl' => $images->productImage($product->getImagePath()),

            // Categoría a la que pertenece el producto.
            'category' => $product->getCategory()
                ? CategoryDto::fromEntity($product->getCategory())
                : null,

            // Listado de alérgenos asociados al producto.
            'allergens' => array_map(
                static fn ($a) => AllergenDto::fromEntity($a),
                $product->getAllergens()->toArray()
            ),
        ];
    }
}