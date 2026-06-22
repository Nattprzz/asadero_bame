<?php

namespace App\Controller\Api\V1\Admin;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\OrderService;
use App\Service\RequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/orders')]
final class OrderAdminController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly OrderService $orderService,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $orders = $this->orders->findVisibleFor($user, true);

        return $this->responses->success(array_map(fn ($order) => $this->presenter->order($order), $orders));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $this->orderService->getVisibleOrder($id, $user, true);

        return $this->responses->success($this->presenter->order($order));
    }

    #[Route('/{id}/estado', methods: ['PATCH'])]
    #[Route('/{id}/status', methods: ['PATCH'])]
    public function status(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $this->payload->fromJson($request);
        $status = (string) ($data['estado'] ?? $data['status'] ?? '');
        $order = $this->orderService->getVisibleOrder($id, $user, true);

        return $this->responses->success($this->presenter->order($this->orderService->transition($order, $status)));
    }
}
