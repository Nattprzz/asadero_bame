<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Enum\Roles;
use App\Repository\OrderRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\OrderService;
use App\Service\RequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/orders')]
final class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly OrderService $orderService,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $this->payload->fromJson($request);
        $data['lines'] ??= $data['items'] ?? null;
        $data['type'] ??= 'takeaway';

        $order = $this->orderService->create($user, $data);
        return $this->responses->success($this->presenter->order($order), 201);
    }

    #[Route('', methods: ['GET'])]
    #[Route('/mis-pedidos', methods: ['GET'])]
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $orders = $this->orders->findVisibleFor($user, $this->isOperational($user));
        return $this->responses->success(array_map(fn ($order) => $this->presenter->order($order), $orders));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $this->orderService->getVisibleOrder($id, $user, $this->isOperational($user));
        return $this->responses->success($this->presenter->order($order));
    }

    #[Route('/{id}/cancel', methods: ['PATCH'])]
    public function cancel(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $this->orderService->getVisibleOrder($id, $user, $this->isOperational($user));
        return $this->responses->success($this->presenter->order($this->orderService->cancel($order, $user, $this->isOperational($user))));
    }

    #[Route('/{id}/status', methods: ['PATCH'])]
    public function status(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(Roles::RESPONSABLE);
        $data = $this->payload->fromJson($request);
        $order = $this->orderService->getVisibleOrder($id, $this->getUser(), true);
        return $this->responses->success($this->presenter->order($this->orderService->transition($order, (string) ($data['status'] ?? ''))));
    }

    private function isOperational(User $user): bool
    {
        return (bool) array_intersect($user->getRoles(), Roles::OPERATIONAL);
    }
}
