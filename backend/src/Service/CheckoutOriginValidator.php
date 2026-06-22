<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CheckoutOriginValidator
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnvironment,
        #[Autowire('%env(CHECKOUT_ALLOWED_ORIGINS)%')]
        private readonly string $checkoutAllowedOrigins = '',
    ) {}

    public function assertConfigured(): void
    {
        $this->allowedOrigins();
    }

    public function assertReturnUrl(string $url): string
    {
        if (trim($url) === '') {
            throw new BadRequestHttpException('successUrl y cancelUrl son obligatorias.');
        }

        $parts = parse_url($url);

        if (
            filter_var($url, FILTER_VALIDATE_URL) === false
            || !is_array($parts)
            || empty($parts['scheme'])
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
        ) {
            throw new BadRequestHttpException('URL de retorno no valida.');
        }

        $parts['scheme'] = mb_strtolower($parts['scheme']);
        $parts['host'] = mb_strtolower($parts['host']);

        if (!in_array($parts['scheme'], ['http', 'https'], true)) {
            throw new BadRequestHttpException('URL de retorno no permitida.');
        }

        foreach ($this->allowedOrigins() as $allowedOrigin) {
            if ($this->matchesOrigin($parts, $allowedOrigin)) {
                return $url;
            }
        }

        throw new BadRequestHttpException('Dominio de retorno no permitido.');
    }

    /**
     * @return array<int, array{scheme:string, host:string, port:?int}>
     */
    private function allowedOrigins(): array
    {
        $rawOrigins = array_values(array_filter(
            array_map('trim', explode(',', $this->checkoutAllowedOrigins)),
            static fn (string $origin): bool => $origin !== ''
        ));

        if ($rawOrigins === []) {
            throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS debe contener al menos un origen valido.');
        }

        $origins = [];

        foreach ($rawOrigins as $origin) {
            if (str_contains($origin, '*')) {
                throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS no admite comodines.');
            }

            $parts = parse_url($origin);

            if (
                filter_var($origin, FILTER_VALIDATE_URL) === false
                || !is_array($parts)
                || empty($parts['scheme'])
                || empty($parts['host'])
                || isset($parts['user'])
                || isset($parts['pass'])
                || isset($parts['query'])
                || isset($parts['fragment'])
                || (isset($parts['path']) && !in_array($parts['path'], ['', '/'], true))
            ) {
                throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS contiene un origen no valido.');
            }

            $scheme = mb_strtolower((string) $parts['scheme']);
            $host = mb_strtolower((string) $parts['host']);
            $port = isset($parts['port']) ? (int) $parts['port'] : null;

            if (!in_array($scheme, ['http', 'https'], true)) {
                throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS solo admite esquemas http o https.');
            }

            if ($this->appEnvironment === 'prod') {
                if ($scheme !== 'https') {
                    throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS exige https en produccion.');
                }

                if ($this->isLocalhost($host)) {
                    throw new \RuntimeException('CHECKOUT_ALLOWED_ORIGINS no permite localhost en produccion.');
                }
            }

            $origins[] = [
                'scheme' => $scheme,
                'host' => $host,
                'port' => $port,
            ];
        }

        return $origins;
    }

    /**
     * @param array{scheme:string,host:string,port?:int|null} $urlParts
     * @param array{scheme:string,host:string,port:?int} $allowedOrigin
     */
    private function matchesOrigin(array $urlParts, array $allowedOrigin): bool
    {
        if ($urlParts['scheme'] !== $allowedOrigin['scheme'] || $urlParts['host'] !== $allowedOrigin['host']) {
            return false;
        }

        $urlPort = $urlParts['port'] ?? null;

        if ($urlPort === null || $allowedOrigin['port'] === null) {
            return $urlPort === $allowedOrigin['port'];
        }

        return $urlPort === $allowedOrigin['port'];
    }

    private function isLocalhost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }
}
