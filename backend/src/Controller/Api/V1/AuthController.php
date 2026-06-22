<?php

// ─────────────────────────────────────────────────────────────────────────────
// AuthController.php — autenticación de usuarios.
//
// Gestiona el registro, inicio y cierre de sesión, consulta del usuario actual
// y recuperación de contraseña mediante token.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Service\ApiResponseFactory;
use App\Service\AuthService;
use App\Service\EntityPresenter;
use App\Service\InputValidator;
use App\Service\RateLimitService;
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
        private readonly RateLimitService $rateLimiter,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Registra un usuario nuevo y devuelve su token de acceso.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $this->rateLimiter->hit($request, 'auth_register', 5, 300);

        [$token, $user] = $this->auth->register(
            $this->payload->fromJson($request)
        );

        return $this->responses->success([
            'token' => $token,
            'user' => $this->presenter->user($user),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Inicia sesión y devuelve el token que usará el frontend.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $this->rateLimiter->hit($request, 'auth_login', 10, 300);

        [$token, $user] = $this->auth->loginFromPayload(
            $this->payload->fromJson($request)
        );

        return $this->responses->success([
            'token' => $token,
            'user' => $this->presenter->user($user),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cierra la sesión invalidando el token enviado en la cabecera.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout(
            (string) $request->headers->get('Authorization')
        );

        return $this->responses->success([
            'loggedOut' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Devuelve los datos del usuario autenticado.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->responses->success(
            $this->presenter->user($user)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Genera un token temporal para recuperar la contraseña.
    //
    // La respuesta no confirma si el correo existe para no exponer usuarios
    // registrados.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/forgot-password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $this->rateLimiter->hit($request, 'auth_forgot_password', 5, 900);

        $data = $this->payload->fromJson($request);

        $this->validator->requireFields($data, ['email']);

        $this->auth->createPasswordResetToken(
            (string) $data['email']
        );

        return $this->responses->success([
            'message' => 'Si el email existe, recibirás instrucciones para restablecer la contraseña.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cambia la contraseña usando el token de recuperación.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/reset-password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $this->payload->fromJson($request);

        $this->validator->requireFields($data, ['token', 'password']);

        $this->auth->resetPassword(
            (string) $data['token'],
            (string) $data['password']
        );

        return $this->responses->success([
            'passwordReset' => true,
        ]);
    }
}
