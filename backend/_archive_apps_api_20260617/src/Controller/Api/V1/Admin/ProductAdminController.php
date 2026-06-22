<?php

namespace App\Controller\Api\V1\Admin;

use App\Entity\Product;
use App\Repository\AllergenRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/products')]
final class ProductAdminController extends AbstractController
{
    public function __construct(private readonly ProductRepository $products, private readonly CategoryRepository $categories, private readonly AllergenRepository $allergens, private readonly EntityManagerInterface $em, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter, private readonly RequestPayload $payload, private readonly InputValidator $validator, private readonly Slugger $slugger) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse { return $this->responses->success(array_map(fn ($p) => $this->presenter->product($p), $this->products->findBy([], ['name' => 'ASC']))); }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $product = new Product();
        $this->apply($product, $this->payload->fromJson($request), true);
        $this->em->persist($product);
        $this->em->flush();
        return $this->responses->success($this->presenter->product($product), 201);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse { return $this->responses->success($this->presenter->product($this->find($id))); }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->find($id);
        $this->apply($product, $this->payload->fromJson($request), false);
        $this->em->flush();
        return $this->responses->success($this->presenter->product($product));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();
        return $this->responses->success(['deleted' => true]);
    }

    private function find(int $id): Product { return $this->products->find($id) ?? throw new NotFoundHttpException('Producto no encontrado.'); }

    private function apply(Product $product, array $data, bool $creating): void
    {
        $categoryId = $data['categoryId'] ?? $data['categoriaId'] ?? null;
        if ($creating && empty($categoryId)) {
            throw new BadRequestHttpException('categoryId o categoriaId es obligatorio.');
        }
        if ($categoryId !== null) {
            $category = $this->categories->find((int) $categoryId);
            if ($category === null) {
                throw new BadRequestHttpException('Categoria no encontrada.');
            }
            $product->setCategory($category);
        }

        $name = (string) ($data['name'] ?? $data['nombre'] ?? $product->getName());
        $availability = (string) ($data['availability'] ?? $product->getAvailability());
        $this->validator->productAvailability($availability);
        $price = $data['price'] ?? $data['precio'] ?? null;
        if ($price !== null && (float) $price < 0) {
            throw new BadRequestHttpException('El precio debe ser mayor o igual que 0.');
        }

        $product->setName($name)
            ->setSlug((string) ($data['slug'] ?? ($product->getSlug() ?: $this->slugger->slug($name))))
            ->setDescription($data['description'] ?? $data['descripcion'] ?? $product->getDescription())
            ->setPrice($data['price'] ?? $data['precio'] ?? $product->getPrice())
            ->setAvailable((bool) ($data['available'] ?? $data['disponible'] ?? $product->isAvailable()))
            ->setAvailability($availability)
            ->setFeatured((bool) ($data['featured'] ?? $product->isFeatured()))
            ->setWeight($data['weight'] ?? $product->getWeight())
            ->setPrepTime(isset($data['prepTime']) ? (int) $data['prepTime'] : $product->getPrepTime())
            ->setImagePath($data['imagePath'] ?? $data['imagen'] ?? $product->getImagePath());

        if (isset($data['allergenIds']) && is_array($data['allergenIds'])) {
            $items = [];
            foreach ($data['allergenIds'] as $id) {
                $allergen = $this->allergens->find((int) $id);
                if ($allergen === null) {
                    throw new BadRequestHttpException('Alergeno no encontrado.');
                }
                $items[] = $allergen;
            }
            $product->setAllergens($items);
        }
    }
}
