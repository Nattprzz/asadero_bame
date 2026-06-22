<?php

// ─────────────────────────────────────────────────────────────────────────────
// LocalController.php — consulta pública de locales.
//
// Permite obtener los locales visibles para los clientes y consultar la
// información de un local concreto.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Repository\LocalRepository;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/locals')]
#[Route('/api/v1/locales')]
final class LocalController extends AbstractController
{
    public function __construct(
        private readonly LocalRepository $locals,
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve los locales públicos disponibles en la aplicación.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->responses->success(
            array_map(
                fn ($l) => $this->presenter->local($l),
                $this->locals->findPublic()
            )
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Muestra la información de un local concreto.
    //
    // Solo se devuelven locales activos para evitar mostrar establecimientos
    // que hayan sido deshabilitados desde administración.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $local = $this->locals->find($id);

        if ($local === null || !$local->isActive()) {
            throw new NotFoundHttpException('Local no encontrado.');
        }

        return $this->responses->success(
            $this->presenter->local($local)
        );
    }
}
