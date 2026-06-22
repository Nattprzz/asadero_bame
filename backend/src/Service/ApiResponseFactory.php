<?php

// ─────────────────────────────────────────────────────────────────────────────
// ApiResponseFactory.php — generación de respuestas JSON.
//
// Este servicio centraliza el formato de las respuestas de la API para
// mantener una estructura uniforme tanto en respuestas exitosas como en
// errores de validación o errores generales.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponseFactory
{
    // Devuelve una respuesta correcta utilizando el código HTTP 200.
    public function ok(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->success($data, 200, $message);
    }

    // Genera una respuesta satisfactoria con el formato estándar de la API.
    public function success(mixed $data = null, int $status = 200, ?string $message = null): JsonResponse
    {
        $payload = [
            'success' => true,
            'data' => $data,
        ];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        return new JsonResponse($payload, $status);
    }

    // Devuelve los errores producidos durante una validación.
    public function validationError(array $errors, string $message = 'Error de validacion.'): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => 'VALIDATION_ERROR',
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => $message,
                'details' => $errors,
            ],
        ], 422);
    }

    // Genera una respuesta de error genérica para cualquier operación fallida.
    public function error(string $message, array|int $errors = [], int $status = 400, string $code = 'BAD_REQUEST'): JsonResponse
    {
        // Permite indicar directamente el código HTTP como segundo parámetro.
        if (is_int($errors)) {
            $status = $errors;
            $errors = [];

            if ($code === 'BAD_REQUEST') {
                $code = match (true) {
                    $status === 401 => 'UNAUTHORIZED',
                    $status === 403 => 'FORBIDDEN',
                    $status === 404 => 'NOT_FOUND',
                    $status === 422 => 'VALIDATION_ERROR',
                    $status >= 500 => 'INTERNAL_ERROR',
                    default => 'REQUEST_ERROR',
                };
            }
        }

        $payload = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if ($errors !== []) {
            $payload['error']['details'] = $errors;
        }

        return new JsonResponse($payload, $status);
    }
}
