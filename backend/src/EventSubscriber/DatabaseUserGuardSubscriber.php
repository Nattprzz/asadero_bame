<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DatabaseUserGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
        #[Autowire('%env(resolve:DATABASE_URL)%')]
        private readonly string $databaseUrl,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2048],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->environment !== 'prod') {
            return;
        }

        if (!self::isAllowedProductionDatabaseUser($this->databaseUrl)) {
            throw new \RuntimeException(
                'DATABASE_URL usa un usuario no permitido en produccion. Usa un usuario de runtime distinto de postgres.'
            );
        }
    }

    public static function isAllowedProductionDatabaseUser(string $databaseUrl): bool
    {
        $username = self::databaseUsername($databaseUrl);

        return $username !== null
            && $username !== ''
            && $username !== 'postgres';
    }

    private static function databaseUsername(string $databaseUrl): ?string
    {
        $parts = parse_url($databaseUrl);

        if (!is_array($parts)) {
            return null;
        }

        $user = $parts['user'] ?? null;

        return is_string($user) && $user !== '' ? mb_strtolower($user) : null;
    }
}
