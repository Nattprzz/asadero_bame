<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Service\ApiResponseFactory;
use App\Service\AuthService;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseFactory $responses,
        private readonly RequestPayload $payload,
        private readonly AuthService $auth,
        private readonly EntityPresenter $presenter,
        private readonly InputValidator $validator,
    ) {}

    #[Route('/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        [$token, $user] = $this->auth->register($this->payload->fromJson($request));
        return $this->responses->success(['token' => $token, 'user' => $this->presenter->user($user)], 201);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = $this->payload->fromJson($request);
        $this->validator->requireFields($data, ['password']);
        $identifier = $data['email'] ?? $data['username'] ?? null;
        if ($identifier === null || trim((string) $identifier) === '') {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Email o username es obligatorio.');
        }

        [$token, $user] = $this->auth->login((string) $identifier, (string) $data['password']);
        return $this->responses->success(['token' => $token, 'user' => $this->presenter->user($user)]);
    }

    #[Route('/logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout((string) $request->headers->get('Authorization'));
        return $this->responses->success(['loggedOut' => true]);
    }

    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->responses->success($this->presenter->user($user));
    }

    #[Route('/forgot-password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $this->payload->fromJson($request);
        $this->validator->requireFields($data, ['email']);
        $token = $this->auth->createPasswordResetToken((string) $data['email']);

        $response = ['message' => 'Si el email existe, se ha generado un token de recuperacion.'];
        if ($token !== null) {
            $response['resetToken'] = $token;
        }

        return $this->responses->success($response);
    }

    #[Route('/reset-password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $this->payload->fromJson($request);
        $this->validator->requireFields($data, ['token', 'password']);
        $this->auth->resetPassword((string) $data['token'], (string) $data['password']);
        return $this->responses->success(['passwordReset' => true]);
    }
}
