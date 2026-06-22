<?php

// ─────────────────────────────────────────────────────────────────────────────
// UserAdminController.php — gestión de usuarios desde administración.
//
// Permite consultar usuarios, actualizar sus datos básicos, modificar sus roles
// y eliminar cuentas desde el panel de administración.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class UserAdminController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EntityManagerInterface $em,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
        private readonly InputValidator $validator
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve los usuarios ordenados desde los más recientes.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->responses->success(
            array_map(
                fn ($u) => $this->presenter->user($u),
                $this->users->findBy([], ['createdAt' => 'DESC'])
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Muestra la información de un usuario concreto.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->responses->success(
            $this->presenter->user($this->find($id))
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza los datos básicos de un usuario.
    //
    // El email se valida aparte porque es un dato sensible para el inicio
    // de sesión y no debería guardarse con un formato incorrecto.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->find($id);
        $data = $this->payload->fromJson($request);

        if (isset($data['email'])) {
            $this->validator->email((string) $data['email']);
            $user->setEmail((string) $data['email']);
        }

        $user->setName((string) ($data['name'] ?? $user->getName()))
            ->setSurname((string) ($data['surname'] ?? $user->getSurname()))
            ->setPhone($data['phone'] ?? $user->getPhone());

        $this->em->flush();

        return $this->responses->success(
            $this->presenter->user($user)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Modifica los roles asignados a un usuario.
    //
    // Antes de guardarlos se valida la lista para evitar roles inexistentes
    // o valores que no estén contemplados por la aplicación.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}/roles', methods: ['PATCH'])]
    public function roles(int $id, Request $request): JsonResponse
    {
        $user = $this->find($id);
        $data = $this->payload->fromJson($request);

        $roles = $data['roles'] ?? [];

        if (!is_array($roles)) {
            $roles = [];
        }

        $this->validator->roleList($roles);

        $user->setRoles($roles);
        $this->em->flush();

        return $this->responses->success(
            $this->presenter->user($user)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Elimina un usuario de la base de datos.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();

        return $this->responses->success([
            'deleted' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Busca un usuario por su identificador y lanza un error si no existe.
    // ─────────────────────────────────────────────────────────────────────────

    private function find(int $id): User
    {
        return $this->users->find($id)
            ?? throw new NotFoundHttpException('Usuario no encontrado.');
    }
}
