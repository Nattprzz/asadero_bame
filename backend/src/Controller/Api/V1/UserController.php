<?php

// ─────────────────────────────────────────────────────────────────────────────
// UserController.php — gestión del perfil del usuario.
//
// Permite consultar y actualizar los datos básicos del usuario autenticado.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Dto\UserDto;
use App\Service\ApiResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/users')]
#[IsGranted('ROLE_USER')]
final class UserController extends BaseApiController
{
    public function __construct(
        private readonly ApiResponseFactory $responses
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve la información del usuario que ha iniciado sesión.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/me', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        return $this->responses->ok(
            UserDto::fromEntity($this->currentUser())
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza los datos editables del perfil del usuario.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/me', methods: ['PUT'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = $this->jsonPayload($request);
        $user = $this->currentUser();

        if (array_key_exists('name', $payload)) {
            $user->setName((string) $payload['name']);
        }

        if (array_key_exists('phone', $payload)) {
            $user->setPhone($payload['phone']);
        }

        // Actualiza la fecha de modificación del perfil.
        $user->touch();

        $em->flush();

        return $this->responses->ok(
            UserDto::fromEntity($user)
        );
    }
}
