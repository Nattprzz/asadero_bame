<?php

namespace App\Controller\Api\V1\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\RequestPayload;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/categories')]
final class CategoryAdminController extends AbstractController
{
    public function __construct(private readonly CategoryRepository $categories, private readonly EntityManagerInterface $em, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter, private readonly RequestPayload $payload, private readonly Slugger $slugger) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse { return $this->responses->success(array_map(fn ($c) => $this->presenter->category($c), $this->categories->findBy([], ['sortOrder' => 'ASC']))); }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $category = new Category();
        $this->apply($category, $this->payload->fromJson($request));
        $this->em->persist($category);
        $this->em->flush();
        return $this->responses->success($this->presenter->category($category), 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $category = $this->find($id);
        $this->apply($category, $this->payload->fromJson($request));
        $this->em->flush();
        return $this->responses->success($this->presenter->category($category));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();
        return $this->responses->success(['deleted' => true]);
    }

    private function find(int $id): Category { return $this->categories->find($id) ?? throw new NotFoundHttpException('Categoria no encontrada.'); }
    private function apply(Category $category, array $data): void
    {
        $category->setName((string) ($data['name'] ?? $data['nombre'] ?? $category->getName()));
        $category->setSlug((string) ($data['slug'] ?? $this->slugger->slug($category->getName())));
        $category->setDescription($data['description'] ?? $data['descripcion'] ?? $category->getDescription());
        $category->setImageUrl($data['imageUrl'] ?? $category->getImageUrl());
        $category->setActive((bool) ($data['active'] ?? $data['activa'] ?? $category->isActive()));
        $category->setSortOrder((int) ($data['sortOrder'] ?? $data['orden'] ?? $category->getSortOrder()));
    }
}
