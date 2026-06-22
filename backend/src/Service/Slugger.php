<?php

// ─────────────────────────────────────────────────────────────────────────────
// Slugger.php — generación de slugs para URLs.
//
// Este servicio transforma textos legibles en identificadores compatibles
// con URLs. El resultado contiene únicamente letras, números y guiones,
// facilitando enlaces más limpios y fáciles de compartir.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

final class Slugger
{
    // Convierte un texto en un slug válido para URLs.
    public function slug(string $value): string
    {
        // Sustituye caracteres especiales y acentos por caracteres ASCII.
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $value);

        $slug = strtolower((string) $slug);

        // Reemplaza caracteres no válidos por guiones.
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        // Si el resultado queda vacío, genera un identificador aleatorio.
        return trim($slug, '-') ?: bin2hex(random_bytes(4));
    }
}