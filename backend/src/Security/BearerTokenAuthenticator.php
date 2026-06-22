<?php

// ─────────────────────────────────────────────────────────────────────────────
// BearerTokenAuthenticator.php — autenticación mediante token Bearer.
//
// Este autenticador valida los tokens enviados en la cabecera Authorization
// y permite identificar al usuario asociado para proteger los endpoints de
// la API.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Security;

use App\Repository\PersonalAccessTokenRepository;
use App\Service\ApiResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class BearerTokenAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly PersonalAccessTokenRepository $tokens,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseFactory $responses,
    ) {}

    // Comprueba si la petición incluye una cabecera Authorization con formato Bearer.
    public function supports(Request $request): ?bool
    {
        return str_starts_with(
            (string) $request->headers->get('Authorization'),
            'Bearer '
        );
    }

    // Valida el token recibido y obtiene el usuario asociado.
    public function authenticate(Request $request): Passport
    {
        $plainToken = trim(
            substr((string) $request->headers->get('Authorization'), 7)
        );

        // Se trabaja con el hash del token por seguridad.
        $hash = hash('sha256', $plainToken);

        return new SelfValidatingPassport(
            new UserBadge($hash, function (string $tokenHash) {
                $token = $this->tokens->findOneBy([
                    'tokenHash' => $tokenHash,
                ]);

                if (
                    $token === null ||
                    !$token->isValid() ||
                    $token->getUser() === null
                ) {
                    throw new AuthenticationException('Token no valido.');
                }

                return $token->getUser();
            })
        );
    }

    // Si la autenticación es correcta, Symfony continúa normalmente.
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null;
    }

    // Respuesta enviada cuando el token es inválido o ha expirado.
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): Response {
        return $this->responses->error('No autenticado.', [], Response::HTTP_UNAUTHORIZED, 'UNAUTHORIZED');
    }

    // Respuesta enviada cuando una ruta requiere autenticación y no se envía token.
    public function start(
        Request $request,
        ?AuthenticationException $authException = null
    ): Response {
        return $this->responses->error('Token Bearer requerido.', [], Response::HTTP_UNAUTHORIZED, 'UNAUTHORIZED');
    }
}
