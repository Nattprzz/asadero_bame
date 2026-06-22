<?php

// ─────────────────────────────────────────────────────────────────────────────
// OrderLineController.php — consulta de líneas de pedido.
//
// Permite acceder a las líneas de los pedidos registrados. Los usuarios solo
// pueden consultar sus propios pedidos, mientras que los administradores tienen
// acceso completo.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Dto\OrderLineDto;
use App\Repository\OrderLineRepository;
use App\Service\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/order-lines')]
#[IsGranted('ROLE_USER')]
final class OrderLineController extends BaseApiController
{
    public function __construct(
        private readonly ApiResponseFactory $responses
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve las líneas de pedido visibles para el usuario actual.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(OrderLineRepository $lines): JsonResponse
    {
        $all = $lines->findVisibleFor($this->currentUser(), $this->isGranted('ROLE_ADMIN'));

        return $this->responses->ok(array_map([OrderLineDto::class, 'fromEntity'], array_values($all)));
    }
    
    // ─────────────────────────────────────────────────────────────────────────
    // Muestra una línea de pedido concreta si el usuario tiene acceso a ella.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, OrderLineRepository $lines): JsonResponse
    {
        $line = $lines->findOneVisibleFor($id, $this->currentUser(), $this->isGranted('ROLE_ADMIN'));
        if (!$line) {
            return $this->responses->error('Order line not found.', 404);
        }

        return $this->responses->ok(OrderLineDto::fromEntity($line));
    }
}
