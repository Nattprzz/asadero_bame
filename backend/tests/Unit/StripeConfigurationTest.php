<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\StripeConfigValidator;
use PHPUnit\Framework\TestCase;

final class StripeConfigurationTest extends TestCase
{
    public function testDevelopmentAcceptsTestKey(): void
    {
        $this->assertConfigurationIsValid('dev', 'sk_test_example', 'whsec_example');
    }

    public function testDevelopmentRejectsLiveKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sk_test_ fuera de produccion');

        $this->assertConfigurationIsValid('dev', 'sk_live_example', 'whsec_example');
    }

    public function testProductionAcceptsLiveKey(): void
    {
        $this->assertConfigurationIsValid('prod', 'sk_live_example', 'whsec_example');
    }

    public function testProductionRejectsTestKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sk_live_ en produccion');

        $this->assertConfigurationIsValid('prod', 'sk_test_example', 'whsec_example');
    }

    public function testProductionRejectsEmptySecretKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('STRIPE_SECRET_KEY no esta configurada');

        $this->assertConfigurationIsValid('prod', '', 'whsec_example');
    }

    public function testProductionRejectsEmptyWebhookSecret(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('STRIPE_WEBHOOK_SECRET no esta configurada correctamente');

        $this->assertConfigurationIsValid('prod', 'sk_live_example', '');
    }

    private function assertConfigurationIsValid(string $environment, string $secretKey, string $webhookSecret): void
    {
        $service = new StripeConfigValidator($environment);
        $service->assertConfigured($secretKey, $webhookSecret);
        $this->addToAssertionCount(1);
    }
}
