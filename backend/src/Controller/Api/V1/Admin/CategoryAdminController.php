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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
final class CategoryAdminController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly EntityManagerInterface $em,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
        private readonly Slugger $slugger,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->responses->success(
            array_map(
                fn (Category $category): array => $this->presenter->category($category),
                $this->categories->findBy([], ['sortOrder' => 'ASC', 'name' => 'ASC'])
            )
        );
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $category = new Category();

        $this->apply($category, $this->payload->fromJson($request));

        $this->em->persist($category);
        $this->em->flush();

        return $this->responses->success(
            $this->presenter->category($category),
            201
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
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

    private function find(int $id): Category
    {
        return $this->categories->find($id)
            ?? throw new NotFoundHttpException('Categoria no encontrada.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function apply(Category $category, array $data): void
    {
        $name = trim((string) ($data['name'] ?? $category->getName()));
        $category->setName($name);
        $category->setSlug((string) ($data['slug'] ?? $this->slugger->slug($name)));
        $category->setDescription($data['description'] ?? $category->getDescription());
        $category->setImageUrl($data['imageUrl'] ?? $data['imagePath'] ?? $category->getImageUrl());

        if (array_key_exists('active', $data)) {
            $category->setActive((bool) $data['active']);
        }

        if (array_key_exists('sortOrder', $data)) {
            $category->setSortOrder((int) $data['sortOrder']);
        }
    }
}
