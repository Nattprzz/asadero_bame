<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\CheckoutOriginValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CheckoutOriginValidationTest extends TestCase
{
    public function testEmptyAllowlistIsRejected(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CHECKOUT_ALLOWED_ORIGINS debe contener');

        $this->validateConfiguration('dev', '');
    }

    public function testLocalhostHttpIsAllowedInDevelopment(): void
    {
        self::assertSame(
            'http://localhost:4200/checkout/success',
            $this->validateReturnUrl(
                'dev',
                'http://localhost:4200, http://127.0.0.1:4200',
                'http://localhost:4200/checkout/success'
            )
        );
    }

    public function testLocalhostIsRejectedInProduction(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('no permite localhost en produccion');

        $this->validateConfiguration('prod', 'https://localhost:4200');
    }

    public function testValidHttpsOriginIsAllowedInProduction(): void
    {
        self::assertSame(
            'https://asaderobame.com/checkout/success?session_id=example',
            $this->validateReturnUrl(
                'prod',
                'https://asaderobame.com',
                'https://asaderobame.com/checkout/success?session_id=example'
            )
        );
    }

    public function testWildcardIsRejected(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('no admite comodines');

        $this->validateConfiguration('prod', 'https://*.asaderobame.com');
    }

    public function testLookalikeMaliciousDomainIsRejected(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Dominio de retorno no permitido');

        $this->validateReturnUrl(
            'prod',
            'https://asaderobame.com',
            'https://asaderobame.com.evil.com/checkout/success'
        );
    }

    public function testSuccessAndCancelUrlsFromConfiguredOriginsAreAllowed(): void
    {
        $service = $this->service('dev', ' http://localhost:4200 , http://127.0.0.1:4200 ');

        self::assertSame(
            'http://localhost:4200/success',
            $service->assertReturnUrl('http://localhost:4200/success')
        );
        self::assertSame(
            'http://127.0.0.1:4200/cancel',
            $service->assertReturnUrl('http://127.0.0.1:4200/cancel')
        );
    }

    public function testRelativeUrlIsRejected(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('URL de retorno no valida');

        $this->validateReturnUrl('dev', 'http://localhost:4200', '/checkout/success');
    }

    public function testJavascriptUrlIsRejected(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('URL de retorno no valida');

        $this->validateReturnUrl('dev', 'http://localhost:4200', 'javascript:alert(1)');
    }

    private function validateConfiguration(string $environment, string $origins): void
    {
        $this->service($environment, $origins)->assertConfigured();
        $this->addToAssertionCount(1);
    }

    private function validateReturnUrl(string $environment, string $origins, string $url): string
    {
        return $this->service($environment, $origins)->assertReturnUrl($url);
    }

    private function service(string $environment, string $origins): CheckoutOriginValidator
    {
        return new CheckoutOriginValidator($environment, $origins);
    }
}
