<?php

namespace App\Controller\Api\V1;

use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\OrderService;
use App\Service\RequestPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/orders')]
#[Route('/api/v1/pedidos')]
#[IsGranted('ROLE_USER')]
final class OrderController extends BaseApiController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
    ) {}

    #[Route('', methods: ['GET'])]
    #[Route('/my-orders', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->currentUser();

        return $this->responses->success(
            array_map(
                fn ($order): array => $this->presenter->order($order),
                $this->orderService->findVisibleOrders($user)
            )
        );
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->responses->success(
            $this->presenter->order(
                $this->orderService->create($this->currentUser(), $this->payload->fromJson($request))
            ),
            201
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->currentUser();

        return $this->responses->success(
            $this->presenter->order(
                $this->orderService->getVisibleOrder($id, $user)
            )
        );
    }

    #[Route('/{id}/cancel', methods: ['PATCH'])]
    #[Route('/{id}/cancelar', methods: ['PATCH'])]
    public function cancel(int $id): JsonResponse
    {
        $user = $this->currentUser();

        return $this->responses->success(
            $this->presenter->order(
                $this->orderService->cancel($id, $user)
            )
        );
    }
}
