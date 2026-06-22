<?php

// ─────────────────────────────────────────────────────────────────────────────
// CategoryController.php — consulta pública de categorías.
//
// Permite obtener las categorías visibles del catálogo y consultar la
// información de una categoría concreta.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Repository\CategoryRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/categories')]
#[Route('/api/v1/categorias')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve las categorías públicas disponibles en el catálogo.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->responses->success(
            array_map(
                fn ($c) => $this->presenter->category($c),
                $this->categories->findPublic()
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Muestra la información de una categoría concreta.
    //
    // Solo se permiten categorías activas para evitar mostrar elementos
    // deshabilitados del catálogo.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $category = $this->categories->find($id);

        if ($category === null || !$category->isActive()) {
            throw new NotFoundHttpException('Categoria no encontrada.');
        }

        return $this->responses->success(
            $this->presenter->category($category)
        );
    }
}
