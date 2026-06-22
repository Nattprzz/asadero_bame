<?php

namespace App\Tests\Unit;

use App\EventSubscriber\ApiExceptionSubscriber;
use App\Service\ApiResponseFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ApiExceptionSubscriberTest extends TestCase
{
    public function testUnexpectedExceptionReturnsJson500WithoutHtml(): void
    {
        $subscriber = new ApiExceptionSubscriber(new ApiResponseFactory());
        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response();
            }
        };
        $request = Request::create('/api/v1/_test/error');
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, new \RuntimeException('Boom'));

        $subscriber->onException($event);

        $response = $event->getResponse();

        self::assertNotNull($response);
        self::assertSame(500, $response->getStatusCode());
        $payload = json_decode((string) $response->getContent(), true);

        self::assertIsArray($payload);
        self::assertFalse($payload['success']);
        self::assertSame('Unexpected server error.', $payload['message']);
        self::assertSame([], $payload['errors']);
        self::assertSame('INTERNAL_ERROR', $payload['code']);
        self::assertSame('INTERNAL_ERROR', $payload['error']['code']);
        self::assertSame('Unexpected server error.', $payload['error']['message']);
        self::assertStringNotContainsString('<html', mb_strtolower((string) $response->getContent()));
    }
}
