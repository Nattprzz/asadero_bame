<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponseFactory
{
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

    public function error(string $message, array $errors = [], int $status = 400, string $code = 'BAD_REQUEST'): JsonResponse
    {
        $payload = [
            'success' => false,
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
