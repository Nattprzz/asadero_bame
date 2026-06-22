<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RequestPayload
{
    public function fromJson(Request $request): array
    {
        $content = trim($request->getContent());
        if ($content === '') {
            return [];
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new BadRequestHttpException('El cuerpo de la peticion debe ser JSON valido.');
        }

        return $data;
    }
}
