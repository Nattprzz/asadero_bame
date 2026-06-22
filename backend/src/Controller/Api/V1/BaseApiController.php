<?php

// ─────────────────────────────────────────────────────────────────────────────
// BaseApiController.php — utilidades comunes para los controladores de la API.
//
// Centraliza funcionalidades reutilizadas, como la lectura del cuerpo JSON
// de las peticiones y la obtención segura del usuario autenticado.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\Request;
use JsonException;

abstract class BaseApiController extends AbstractController
{
    // ─────────────────────────────────────────────────────────────────────────
    // Convierte el cuerpo de la petición a un array asociativo.
    //
    // Si el JSON recibido no es válido se lanza una excepción para evitar
    // trabajar con datos incorrectos.
    // ─────────────────────────────────────────────────────────────────────────

    protected function jsonPayload(Request $request): array
    {
        try {
            $payload = json_decode($request->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new \InvalidArgumentException('Invalid JSON body.');
        }

        if (!is_array($payload)) {
            throw new \InvalidArgumentException('Invalid JSON body.');
        }

        return $payload;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve el usuario autenticado garantizando su tipo.
    //
    // Este método evita repetir comprobaciones en cada controlador que
    // necesite acceder al usuario actual.
    // ─────────────────────────────────────────────────────────────────────────

    protected function currentUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('Bearer', 'Authenticated user not found.');
        }

        return $user;
    }
}
