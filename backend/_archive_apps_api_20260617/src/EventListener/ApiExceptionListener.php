<?php

namespace App\EventListener;

use App\Service\ApiResponseFactory;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsEventListener]
final class ApiExceptionListener
{
    public function __construct(private readonly ApiResponseFactory $responses) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $exception = $event->getThrowable();
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $message = $status >= 500 ? 'Error interno del servidor.' : $exception->getMessage();
        $code = $this->errorCode($exception, $status);
        $errors = [];

        if ($status === 400) {
            $decoded = json_decode($exception->getMessage(), true);
            if (is_array($decoded)) {
                $message = 'Error de validacion.';
                $code = 'VALIDATION_ERROR';
                $errors = $decoded;
            }
        }

        $event->setResponse($this->responses->error($message, $errors, $status, $code));
    }

    private function errorCode(\Throwable $exception, int $status): string
    {
        return match (true) {
            $exception instanceof BadRequestHttpException => 'VALIDATION_ERROR',
            $exception instanceof UnauthorizedHttpException => 'UNAUTHORIZED',
            $exception instanceof AccessDeniedHttpException => 'FORBIDDEN',
            $exception instanceof NotFoundHttpException => 'NOT_FOUND',
            $status >= 500 => 'INTERNAL_ERROR',
            default => 'REQUEST_ERROR',
        };
    }
}
