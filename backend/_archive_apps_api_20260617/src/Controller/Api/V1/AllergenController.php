<?php

namespace App\Controller\Api\V1;

use App\Repository\AllergenRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/allergens')]
final class AllergenController extends AbstractController
{
    public function __construct(private readonly AllergenRepository $allergens, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->responses->success(array_map(fn ($a) => $this->presenter->allergen($a), $this->allergens->findBy([], ['name' => 'ASC'])));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $allergen = $this->allergens->find($id);
        if ($allergen === null) {
            throw new NotFoundHttpException('Alergeno no encontrado.');
        }
        return $this->responses->success($this->presenter->allergen($allergen));
    }
}
