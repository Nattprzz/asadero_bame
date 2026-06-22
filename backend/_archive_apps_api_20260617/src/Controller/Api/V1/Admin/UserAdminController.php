<?php

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

#[Route('/api/v1/admin/users')]
final class UserAdminController extends AbstractController
{
    public function __construct(private readonly UserRepository $users, private readonly EntityManagerInterface $em, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter, private readonly RequestPayload $payload, private readonly InputValidator $validator) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse { return $this->responses->success(array_map(fn ($u) => $this->presenter->user($u), $this->users->findBy([], ['createdAt' => 'DESC']))); }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse { return $this->responses->success($this->presenter->user($this->find($id))); }

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
        return $this->responses->success($this->presenter->user($user));
    }

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
        return $this->responses->success($this->presenter->user($user));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();
        return $this->responses->success(['deleted' => true]);
    }

    private function find(int $id): User { return $this->users->find($id) ?? throw new NotFoundHttpException('Usuario no encontrado.'); }
}
