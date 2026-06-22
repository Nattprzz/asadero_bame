<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderAdminController.php — gestión de pedidos desde administración.
//
// Permite consultar los pedidos registrados en el sistema, visualizar su
// información completa y actualizar su estado.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1\Admin;

use App\Entity\User;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use App\Service\AdminLocalScopeResolver;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\OrderService;
use App\Service\RequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/orders')]
#[Route('/api/v1/admin/pedidos')]
#[IsGranted('ROLE_RESPONSABLE')]
final class OrderAdminController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly OrderService $orderService,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
        private readonly AdminLocalScopeResolver $localScopeResolver,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve todos los pedidos visibles para el administrador.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $orders = $this->orders->findForAdmin(
            $this->localScopeResolver->resolve($user, $request->query->get('localId') ?? $request->query->get('local_id'))
        );

        return $this->responses->success(
            array_map(
                fn ($order) => $this->presenter->order($order),
                $orders
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Muestra el detalle completo de un pedido concreto.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $order = $this->orderService->getVisibleOrder($id, $user);

        return $this->responses->success(
            $this->presenter->order($order)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza el estado de un pedido.
    //
    // Se aceptan tanto las claves "estado" como "status" para facilitar la
    // integración con distintos clientes.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}/estado', methods: ['PATCH'])]
    #[Route('/{id}/status', methods: ['PATCH'])]
    public function status(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = $this->payload->fromJson($request);
        $status = (string) ($data['estado'] ?? $data['status'] ?? '');

        if (in_array(mb_strtolower(trim($status)), [OrderStatus::CANCELLED, 'cancelado'], true)) {
            return $this->responses->success(
                $this->presenter->order(
                    $this->orderService->cancel($id, $user)
                )
            );
        }

        $order = $this->orderService->getVisibleOrder($id, $user);

        return $this->responses->success(
            $this->presenter->order(
                $this->orderService->transition($order, $status)
            )
        );
    }

}
