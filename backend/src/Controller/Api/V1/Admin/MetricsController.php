<?php

// ─────────────────────────────────────────────────────────────────────────────
// MetricsController.php — métricas del panel de administración.
//
// Centraliza los datos utilizados por el dashboard, como pedidos, ingresos,
// usuarios registrados y productos disponibles.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1\Admin;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin')]
#[IsGranted('ROLE_ADMIN')]
final class MetricsController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly UserRepository $users,
        private readonly ProductRepository $products,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve las métricas principales mostradas en el panel de administración.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/metrics', methods: ['GET'])]
    public function metrics(): JsonResponse
    {
        $recent = $this->orders->recent(5);

        return $this->responses->success([
            'totalOrders' => $this->orders->count([]),
            'todayOrders' => $this->orders->countToday(),
            'totalIncome' => (float) $this->orders->income(),
            'todayIncome' => (float) $this->orders->income(new \DateTimeImmutable('today')),
            'registeredUsers' => $this->users->count([]),
            'activeProducts' => $this->products->count(['available' => true]),
            'recentOrders' => array_map(
                fn ($order) => $this->presenter->order($order),
                $recent
            ),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve los últimos pedidos registrados en el sistema.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/recent-orders', methods: ['GET'])]
    public function recentOrders(): JsonResponse
    {
        return $this->responses->success(
            array_map(
                fn ($order) => $this->presenter->order($order),
                $this->orders->recent(10)
            )
        );
    }
}
