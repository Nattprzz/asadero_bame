<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MeController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseFactory $responses,
        private readonly EntityPresenter $presenter,
    ) {}

    #[Route('/api/v1/me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->responses->success($this->presenter->user($user));
    }
}
