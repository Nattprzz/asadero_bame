<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalAdminController.php — gestión de locales desde el panel de administración.
//
// Permite listar, crear, modificar y eliminar los locales disponibles en la
// aplicación, incluyendo información de contacto, ubicación y estado.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1\Admin;

use App\Entity\Local;
use App\Entity\User;
use App\Repository\LocalRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use App\Service\AdminLocalScopeResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/locals')]
#[Route('/api/v1/admin/locales')]
#[IsGranted('ROLE_GERENTE')]
final class LocalAdminController extends AbstractController
{
    public function __construct(
        private readonly LocalRepository $locals,
        private readonly EntityManagerInterface $em,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
        private readonly RequestPayload $payload,
        private readonly InputValidator $validator,
        private readonly AdminLocalScopeResolver $localScopeResolver
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve el listado completo de locales ordenado por nombre.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->responses->success(
            array_map(
                fn ($l) => $this->presenter->local($l),
                $this->locals->findBy([], ['name' => 'ASC'])
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Crea un nuevo local utilizando los datos recibidos en la petición.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $local = new Local();

        $this->apply($local, $this->payload->fromJson($request));

        $this->em->persist($local);
        $this->em->flush();

        return $this->responses->success(
            $this->presenter->local($local),
            201
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Actualiza la información de un local existente.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->localScopeResolver->resolve($user, $id);
        $local = $this->find($id);

        $this->apply($local, $this->payload->fromJson($request));

        $this->em->flush();

        return $this->responses->success(
            $this->presenter->local($local)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Elimina un local de la base de datos.
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
    // Busca un local por su identificador y lanza un error si no existe.
    // ─────────────────────────────────────────────────────────────────────────

    private function find(int $id): Local
    {
        return $this->locals->find($id)
            ?? throw new NotFoundHttpException('Local no encontrado.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Asigna y valida los datos recibidos antes de guardarlos en la entidad.
    // ─────────────────────────────────────────────────────────────────────────

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
            ->setHours(
                is_array($data['hours'] ?? $data['horario'] ?? null)
                    ? ($data['hours'] ?? $data['horario'])
                    : $local->getHours()
            )
            ->setReservationHours(
                is_array($data['reservationHours'] ?? $data['horarioReservas'] ?? null)
                    ? ($data['reservationHours'] ?? $data['horarioReservas'])
                    : $local->getReservationHours()
            )
            ->setActive((bool) ($data['active'] ?? $local->isActive()))
            ->setStatus($status)
            ->setWhatsapp($data['whatsapp'] ?? $local->getWhatsapp());
    }
}
