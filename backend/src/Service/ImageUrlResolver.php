<?php

// ─────────────────────────────────────────────────────────────────────────────
// ImageUrlResolver.php — generación de URLs para imágenes.
//
// Este servicio transforma las rutas almacenadas en la base de datos en URLs
// accesibles desde el navegador. También permite utilizar directamente URLs
// externas cuando la imagen ya se encuentra alojada en otro servidor.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

final class ImageUrlResolver
{
    public function __construct(private readonly string $baseUrl) {}

    // Genera la URL pública de una imagen de producto.
    public function productImage(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Si ya es una URL completa, se devuelve sin modificaciones.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Codifica correctamente cada segmento de la ruta.
        $encodedPath = implode(
            '/',
            array_map(
                'rawurlencode',
                explode('/', str_replace('\\', '/', ltrim($path, '/')))
            )
        );

        return rtrim($this->baseUrl, '/').'/'.$encodedPath;
    }
}