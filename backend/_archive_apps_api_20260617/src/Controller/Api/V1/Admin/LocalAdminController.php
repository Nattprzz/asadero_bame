<?php

namespace App\Controller\Api\V1\Admin;

use App\Entity\Local;
use App\Repository\LocalRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/locals')]
final class LocalAdminController extends AbstractController
{
    public function __construct(private readonly LocalRepository $locals, private readonly EntityManagerInterface $em, private readonly ApiResponseFactory $responses, private readonly EntityPresenter $presenter, private readonly RequestPayload $payload, private readonly InputValidator $validator) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse { return $this->responses->success(array_map(fn ($l) => $this->presenter->local($l), $this->locals->findBy([], ['name' => 'ASC']))); }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $local = new Local();
        $this->apply($local, $this->payload->fromJson($request));
        $this->em->persist($local);
        $this->em->flush();
        return $this->responses->success($this->presenter->local($local), 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $local = $this->find($id);
        $this->apply($local, $this->payload->fromJson($request));
        $this->em->flush();
        return $this->responses->success($this->presenter->local($local));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->em->remove($this->find($id));
        $this->em->flush();
        return $this->responses->success(['deleted' => true]);
    }

    private function find(int $id): Local { return $this->locals->find($id) ?? throw new NotFoundHttpException('Local no encontrado.'); }
    private function apply(Local $local, array $data): void
    {
        $status = (string) ($data['status'] ?? $local->getStatus());
        $this->validator->localStatus($status);
        $local->setName((string) ($data['name'] ?? $data['nombre'] ?? $local->getName()))
            ->setAddress((string) ($data['address'] ?? $data['direccion'] ?? $local->getAddress()))
            ->setCity((string) ($data['city'] ?? $local->getCity()))
            ->setPostalCode($data['postalCode'] ?? $local->getPostalCode())
            ->setPhone((string) ($data['phone'] ?? $data['telefono'] ?? $local->getPhone()))
            ->setEmail($data['email'] ?? $local->getEmail())
            ->setLatitude($data['latitude'] ?? $local->getLatitude())
            ->setLongitude($data['longitude'] ?? $local->getLongitude())
            ->setHours(is_array($data['hours'] ?? $data['horario'] ?? null) ? ($data['hours'] ?? $data['horario']) : $local->getHours())
            ->setActive((bool) ($data['active'] ?? $local->isActive()))
            ->setStatus($status)
            ->setWhatsapp($data['whatsapp'] ?? $local->getWhatsapp());
    }
}
