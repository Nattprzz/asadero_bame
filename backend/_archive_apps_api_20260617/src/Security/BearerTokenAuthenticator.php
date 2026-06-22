<?php

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

    public function supports(Request $request): ?bool
    {
        return str_starts_with((string) $request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $plainToken = trim(substr((string) $request->headers->get('Authorization'), 7));
        $hash = hash('sha256', $plainToken);

        return new SelfValidatingPassport(new UserBadge($hash, function (string $tokenHash) {
            $token = $this->tokens->findOneBy(['tokenHash' => $tokenHash]);
            if ($token === null || !$token->isValid() || $token->getUser() === null) {
                throw new AuthenticationException('Token no valido.');
            }

            return $token->getUser();
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->responses->error('No autenticado.', [], 401, 'UNAUTHORIZED');
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return $this->responses->error('Token Bearer requerido.', [], 401, 'UNAUTHORIZED');
    }
}
