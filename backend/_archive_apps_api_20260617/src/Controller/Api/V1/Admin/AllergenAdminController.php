<?php

namespace App\Controller\Api\V1\Admin;

use App\Entity\Allergen;
use App\Repository\AllergenRepository;
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

#[Route('/api/v1/admin/allergens')]
final class AllergenAdminController extends AbstractController
{
    public function __construct(private readonly AllergenRepository $allergens, private readonly EntityManagerInterface $em, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter, private readonly RequestPayload $payload, private readonly Slugger $slugger) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse { return $this->responses->success(array_map(fn ($a) => $this->presenter->allergen($a), $this->allergens->findBy([], ['name' => 'ASC']))); }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $allergen = new Allergen();
        $this->apply($allergen, $this->payload->fromJson($request));
        $this->em->persist($allergen);
        $this->em->flush();
        return $this->responses->success($this->presenter->allergen($allergen), 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $allergen = $this->find($id);
        $this->apply($allergen, $this->payload->fromJson($request));
        $this->em->flush();
        return $this->responses->success($this->presenter->allergen($allergen));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();
        return $this->responses->success(['deleted' => true]);
    }

    private function find(int $id): Allergen { return $this->allergens->find($id) ?? throw new NotFoundHttpException('Alergeno no encontrado.'); }
    private function apply(Allergen $allergen, array $data): void
    {
        $allergen->setName((string) ($data['name'] ?? $allergen->getName()));
        $allergen->setSlug((string) ($data['slug'] ?? $this->slugger->slug($allergen->getName())));
        $allergen->setDescription($data['description'] ?? $allergen->getDescription());
        $allergen->setIconUrl($data['iconUrl'] ?? $allergen->getIconUrl());
    }
}
