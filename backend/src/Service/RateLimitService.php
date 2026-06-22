<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class RateLimitService
{
    public function __construct(
        private readonly CacheItemPoolInterface $cacheApp,
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {}

    public function hit(Request $request, string $scope, int $limit, int $windowSeconds): void
    {
        if ($this->environment === 'test') {
            return;
        }

        $ip = $request->getClientIp() ?: 'unknown';
        $key = 'rate_limit_'.hash('sha256', $scope.'|'.$ip);
        $item = $this->cacheApp->getItem($key);
        $hits = $item->isHit() ? (int) $item->get() : 0;

        if ($hits >= $limit) {
            throw new TooManyRequestsHttpException($windowSeconds, 'Demasiadas peticiones. Intentalo de nuevo mas tarde.');
        }

        $item->set($hits + 1);
        $item->expiresAfter($windowSeconds);
        $this->cacheApp->save($item);
    }
}
