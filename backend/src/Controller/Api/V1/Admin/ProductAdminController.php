<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductAdminController.php — gestión de productos desde administración.
//
// Permite administrar los productos del catálogo, incluyendo su categoría,
// precio, disponibilidad, imagen y alérgenos asociados.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1\Admin;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Local;
use App\Entity\LocalProduct;
use App\Repository\AllergenRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\LocalRepository;
use App\Repository\LocalProductRepository;
use App\Service\ApiResponseFactory;
use App\Service\AdminLocalScopeResolver;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/products')]
#[Route('/api/v1/admin/productos')]
#[IsGranted('ROLE_RESPONSABLE')]
final class ProductAdminController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly LocalRepository $locals,
        private readonly LocalProductRepository $localProducts,
        private readonly CategoryRepository $categories,
        private readonly AllergenRepository $allergens,
        private readonly EntityManagerInterface $em,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
        private readonly InputValidator $validator,
        private readonly Slugger $slugger,
        private readonly AdminLocalScopeResolver $localScopeResolver
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve todos los productos ordenados por nombre.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $localId = $this->localScopeResolver->resolve(
            $user,
            $request->query->get('localId') ?? $request->query->get('local_id')
        );
        $local = $localId === null ? null : $this->findLocal($localId);
        $stockByProduct = [];

        if ($local !== null) {
            foreach ($this->localProducts->findByLocalWithProducts($local) as $stock) {
                $productId = $stock->getProduct()?->getId();
                if ($productId !== null) {
                    $stockByProduct[$productId] = $stock;
                }
            }
        }

        return $this->responses->success(
            array_map(
                fn (Product $product) => $this->presentProduct(
                    $product,
                    $stockByProduct[$product->getId()] ?? null,
                    $local !== null
                ),
                $this->products->findForAdmin()
            )
        );
    }

    #[Route('/{id}/stock', methods: ['PATCH'])]
    public function updateStock(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_GERENTE');
        $data = $this->payload->fromJson($request);
        $stockValue = filter_var($data['stock'] ?? null, FILTER_VALIDATE_INT);

        if ($stockValue === false || $stockValue < 0) {
            throw new BadRequestHttpException('El stock debe ser un entero mayor o igual que 0.');
        }

        $product = $this->find($id);
        $local = $this->resolveLocal($data['localId'] ?? $data['local_id'] ?? null);
        $stock = $this->findOrCreateLocalProduct($local, $product);
        $stock->setStock($stockValue);
        $this->em->flush();

        return $this->responses->success($this->presentProduct($product, $stock, true));
    }

    #[Route('/{id}/availability', methods: ['PATCH'])]
    #[Route('/{id}/disponibilidad', methods: ['PATCH'])]
    public function updateAvailability(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_GERENTE');
        $data = $this->payload->fromJson($request);

        if (!array_key_exists('available', $data) && !array_key_exists('disponible', $data)) {
            throw new BadRequestHttpException('El campo disponible es obligatorio.');
        }

        $product = $this->find($id);
        $local = $this->resolveLocal($data['localId'] ?? $data['local_id'] ?? null);
        $stock = $this->findOrCreateLocalProduct($local, $product);
        $stock->setAvailable((bool) ($data['available'] ?? $data['disponible']));
        $this->em->flush();

        return $this->responses->success($this->presentProduct($product, $stock, true));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea un nuevo producto dentro del catálogo.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();

        $this->apply($product, $this->payload->fromJson($request), true);

        $this->em->persist($product);
        $this->em->flush();

        return $this->responses->success(
            $this->presenter->product($product),
            201
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Muestra el detalle de un producto concreto.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->find($id);
        $this->assertProductVisibleForCurrentUser($product);

        return $this->responses->success(
            $this->presenter->product($product)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza la información de un producto existente.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = $this->find($id);

        $this->apply($product, $this->payload->fromJson($request), false);

        $this->em->flush();

        return $this->responses->success(
            $this->presenter->product($product)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Elimina un producto del catálogo.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->em->remove($this->find($id));
        $this->em->flush();

        return $this->responses->success([
            'deleted' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Busca un producto por su identificador y lanza un error si no existe.
    // ─────────────────────────────────────────────────────────────────────────

    private function find(int $id): Product
    {
        return $this->products->find($id)
            ?? throw new NotFoundHttpException('Producto no encontrado.');
    }

    private function findLocal(int $id): Local
    {
        return $this->locals->find($id)
            ?? throw new NotFoundHttpException('Local no encontrado.');
    }

    private function resolveLocal(mixed $requestedLocalId): Local
    {
        /** @var User $user */
        $user = $this->getUser();
        $localId = $this->localScopeResolver->resolve($user, $requestedLocalId);

        if ($localId === null) {
            throw new BadRequestHttpException('localId es obligatorio.');
        }

        return $this->findLocal($localId);
    }

    private function findOrCreateLocalProduct(Local $local, Product $product): LocalProduct
    {
        $stock = $this->localProducts->findOneBy(['local' => $local, 'product' => $product]);

        if ($stock === null) {
            $stock = (new LocalProduct())
                ->setLocal($local)
                ->setProduct($product)
                ->setStock(0)
                ->setAvailable(true);
            $this->em->persist($stock);
        }

        return $stock;
    }

    private function presentProduct(Product $product, ?LocalProduct $stock, bool $localContext): array
    {
        return [
            ...$this->presenter->product($product),
            'stock' => $localContext ? ($stock?->getStock() ?? 0) : null,
            'available' => $localContext
                ? ($stock?->isAvailable() ?? true)
                : $product->isAvailable(),
        ];
    }

    private function assertProductVisibleForCurrentUser(Product $product): void
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        /** @var User $user */
        $user = $this->getUser();
        $localId = $user->getLocal()?->getId();

        if ($localId === null || !$this->products->belongsToLocal($product, $localId)) {
            throw new AccessDeniedHttpException('No puedes acceder a productos de otro local.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Aplica los datos recibidos al producto y valida los campos importantes.
    //
    // Se usa tanto al crear como al actualizar para evitar repetir lógica en
    // los endpoints.
    // ─────────────────────────────────────────────────────────────────────────

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
