<?php

namespace App\EventSubscriber;

use App\Service\ApiResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ApiResponseFactory $responses)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onException', -128],
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $throwable = $event->getThrowable();
        [$status, $code, $message, $details] = $this->normalizeException($throwable);

        $event->setResponse($this->responses->error($message, $details, $status, $code));
    }

    /**
     * @return array{0:int,1:string,2:string,3:array<string, mixed>}
     */
    private function normalizeException(\Throwable $throwable): array
    {
        if ($throwable instanceof AccessDeniedException) {
            return [Response::HTTP_FORBIDDEN, 'FORBIDDEN', 'Forbidden.', []];
        }

        if ($throwable instanceof UnauthorizedHttpException) {
            return [Response::HTTP_UNAUTHORIZED, 'UNAUTHORIZED', 'No autenticado.', []];
        }

        if ($throwable instanceof NotFoundHttpException) {
            return [Response::HTTP_NOT_FOUND, 'NOT_FOUND', 'Resource not found.', []];
        }

        if ($throwable instanceof BadRequestHttpException) {
            $decoded = json_decode($throwable->getMessage(), true);

            if (is_array($decoded)) {
                return [
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'VALIDATION_ERROR',
                    'Revisa los datos del formulario.',
                    $decoded,
                ];
            }

            if ($this->looksLikeMalformedJson($throwable->getMessage())) {
                return [Response::HTTP_BAD_REQUEST, 'BAD_REQUEST', 'Request malformed.', []];
            }

            return [Response::HTTP_UNPROCESSABLE_ENTITY, 'BUSINESS_RULE', $throwable->getMessage(), []];
        }

        if ($throwable instanceof \JsonException) {
            return [Response::HTTP_BAD_REQUEST, 'BAD_REQUEST', 'Request malformed.', []];
        }

        if ($throwable instanceof \InvalidArgumentException) {
            $message = $this->looksLikeMalformedJson($throwable->getMessage())
                ? 'Request malformed.'
                : $throwable->getMessage();

            return [Response::HTTP_BAD_REQUEST, 'BAD_REQUEST', $message, []];
        }

        if ($throwable instanceof HttpExceptionInterface) {
            $status = $throwable->getStatusCode();

            return [
                $status,
                $this->httpCodeToErrorCode($status),
                $status >= 500 ? 'Unexpected server error.' : $throwable->getMessage(),
                [],
            ];
        }

        return [
            Response::HTTP_INTERNAL_SERVER_ERROR,
            'INTERNAL_ERROR',
            'Unexpected server error.',
            [],
        ];
    }

    private function looksLikeMalformedJson(string $message): bool
    {
        return in_array($message, [
            'El cuerpo de la peticion debe ser JSON valido.',
            'Invalid JSON body.',
        ], true);
    }

    private function httpCodeToErrorCode(int $status): string
    {
        return match (true) {
            $status === Response::HTTP_BAD_REQUEST => 'BAD_REQUEST',
            $status === Response::HTTP_UNAUTHORIZED => 'UNAUTHORIZED',
            $status === Response::HTTP_FORBIDDEN => 'FORBIDDEN',
            $status === Response::HTTP_NOT_FOUND => 'NOT_FOUND',
            $status >= 500 => 'INTERNAL_ERROR',
            default => 'REQUEST_ERROR',
        };
    }
}
