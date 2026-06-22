<?php

// ─────────────────────────────────────────────────────────────────────────────
// AdminMetricsService.php — métricas del panel de administración.
//
// Centraliza la obtención de estadísticas generales utilizadas en el dashboard
// administrativo de la aplicación.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

final class AdminMetricsService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly ProductRepository $products,
        private readonly OrderRepository $orders,
        private readonly OrderLineRepository $lines,
    ) {}

    // Recopila las métricas principales mostradas en el panel de administración.
    public function metrics(): array
    {
        return [
            // Número total de usuarios registrados.
            'users' => $this->users->count([]),

            // Número total de productos del catálogo.
            'products' => $this->products->count([]),

            // Número total de pedidos realizados.
            'orders' => $this->orders->count([]),

            // Facturación estimada calculada a partir de los pedidos.
            'estimatedRevenue' => (float) $this->orders->income(),

            // Productos más vendidos.
            'topProducts' => $this->lines->topProducts(),
        ];
    }
}
