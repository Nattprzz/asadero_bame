<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class StripeConfigValidator
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnvironment,
    ) {}

    public function assertConfigured(string $secretKey, string $webhookSecret): void
    {
        $this->assertSecretKey($secretKey);
        $this->assertWebhookSecret($webhookSecret);
    }

    public function assertSecretKey(string $secretKey): void
    {
        $secretKey = trim($secretKey);

        if ($secretKey === '') {
            throw new \RuntimeException('STRIPE_SECRET_KEY no esta configurada correctamente.');
        }

        if ($this->appEnvironment === 'prod') {
            if (!str_starts_with($secretKey, 'sk_live_')) {
                throw new \RuntimeException('STRIPE_SECRET_KEY debe usar sk_live_ en produccion.');
            }

            return;
        }

        if (!str_starts_with($secretKey, 'sk_test_')) {
            throw new \RuntimeException('STRIPE_SECRET_KEY debe usar sk_test_ fuera de produccion.');
        }
    }

    public function assertWebhookSecret(string $webhookSecret): void
    {
        if (trim($webhookSecret) === '') {
            throw new \RuntimeException('STRIPE_WEBHOOK_SECRET no esta configurada correctamente.');
        }
    }
}
