<?php

namespace App\Controller\Api\V1;

use App\Repository\ProductRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/products')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->query->get('category') ?? $request->query->get('categoryId'),
            'availability' => $request->query->get('availability'),
            'featured' => $request->query->has('featured') ? filter_var($request->query->get('featured'), FILTER_VALIDATE_BOOLEAN) : null,
            'search' => $request->query->get('search') ?? $request->query->get('q'),
            'allergens' => $request->query->get('allergens'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
        ];

        return $this->responses->success(array_map(
            fn ($product) => $this->presenter->product($product),
            $this->products->searchPublic(array_filter($filters, fn ($v) => $v !== null && $v !== ''))
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->products->find($id);
        if ($product === null || !$product->isAvailable() || $product->getAvailability() === 'hidden') {
            throw new NotFoundHttpException('Producto no encontrado.');
        }

        return $this->responses->success($this->presenter->product($product));
    }
}
