<?php

// ─────────────────────────────────────────────────────────────────────────────
// AdminController.php — operaciones generales del panel de administración.
//
// Agrupa endpoints básicos para consultar usuarios, modificar roles y obtener
// métricas generales del sistema.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use App\Service\AdminMetricsService;
use App\Service\ApiResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends BaseApiController
{
    public function __construct(private readonly ApiResponseFactory $responses) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve el listado de usuarios para la zona de administración.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/users', methods: ['GET'])]
    public function users(UserRepository $users): JsonResponse
    {
        return $this->responses->ok(
            array_map(
                [UserDto::class, 'fromEntity'],
                $users->findBy([], ['id' => 'ASC'])
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza el rol principal de un usuario.
    //
    // Solo se aceptan los roles definidos por la aplicación para evitar valores
    // no válidos en la base de datos.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/users/{id}/role', methods: ['PUT'])]
    public function role(
        int $id,
        Request $request,
        UserRepository $users,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $users->find($id);

        if (!$user) {
            return $this->responses->error('User not found.', 404);
        }

        $payload = $this->jsonPayload($request);
        $role = (string) ($payload['role'] ?? '');

        if (!in_array($role, ['ROLE_USER', 'ROLE_ADMIN'], true)) {
            return $this->responses->validationError([
                'role' => ['Role must be ROLE_USER or ROLE_ADMIN.'],
            ]);
        }

        $user->setRoles([$role])->touch();
        $em->flush();

        return $this->responses->ok(
            UserDto::fromEntity($user)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve las métricas principales del sistema.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/metrics', methods: ['GET'])]
    public function metrics(AdminMetricsService $metrics): JsonResponse
    {
        return $this->responses->ok(
            $metrics->metrics()
        );
    }
}