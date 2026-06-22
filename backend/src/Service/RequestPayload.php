<?php

// ─────────────────────────────────────────────────────────────────────────────
// RequestPayload.php — lectura de peticiones JSON.
//
// Este servicio se encarga de extraer y convertir el contenido JSON de una
// petición HTTP a un array PHP. También valida que el formato recibido sea
// correcto antes de procesarlo.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RequestPayload
{
    // Obtiene y decodifica el cuerpo JSON de la petición.
    public function fromJson(Request $request): array
    {
        $content = trim($request->getContent());

        // Permite procesar peticiones sin contenido.
        if ($content === '') {
            return [];
        }

        $data = json_decode($content, true);

        // Comprueba que el JSON recibido tenga una estructura válida.
        if (!is_array($data)) {
            throw new BadRequestHttpException(
                'El cuerpo de la peticion debe ser JSON valido.'
            );
        }

        return $data;
    }
}