<?php

// ─────────────────────────────────────────────────────────────────────────────
// StripePaymentController.php — gestión de pagos con Stripe.
//
// Este controlador permite crear sesiones de pago mediante Stripe Checkout
// y recibir los webhooks enviados por Stripe cuando cambia el estado de una
// transacción.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Controller\Api\Payments;

use App\Entity\User;
use App\Service\ApiResponseFactory;
use App\Service\EntityPresenter;
use App\Service\RateLimitService;
use App\Service\RequestPayload;
use App\Service\StripePaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

// ─── Pagos Stripe ─────────────────────────────────────────────────────────────

#[Route('/api/payments/stripe')]
final class StripePaymentController extends AbstractController
{
    public function __construct(
        private readonly StripePaymentService $payments,
        private readonly ApiResponseFactory $responses,
        private readonly RequestPayload $payload,
        private readonly RateLimitService $rateLimiter,
        private readonly EntityPresenter $presenter,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Crea una sesión de Stripe Checkout para que el usuario pueda realizar
    // el pago desde la página segura de Stripe.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/create-checkout-session', methods: ['POST'])]
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $this->rateLimiter->hit($request, 'stripe_checkout', 10, 300);

        // Comprueba que el usuario ha iniciado sesión.
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException(
                'Bearer',
                'Debes iniciar sesión para crear el pago.'
            );
        }

        // Genera la sesión de pago utilizando los datos enviados.
        $result = $this->payments->createCheckoutSession(
            $user,
            $this->payload->fromJson($request),
            $request->headers->get('Idempotency-Key')
        );

        $data = [
            'order' => $this->presenter->order($result['order']),
            'paymentMethod' => $result['paymentMethod'],
            'paymentStatus' => $result['paymentStatus'],
            'requiresOnlinePayment' => $result['requiresOnlinePayment'],
        ];

        if (isset($result['checkoutUrl'])) {
            $data['checkoutUrl'] = $result['checkoutUrl'];
        }

        return $this->responses->success($data, 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Recibe los eventos enviados por Stripe mediante webhooks.
    //
    // Stripe utiliza este endpoint para notificar cambios de estado en los
    // pagos sin necesidad de que intervenga el usuario.
    // ─────────────────────────────────────────────────────────────────────────

    #[Route('/webhook', methods: ['POST'])]
    public function webhook(Request $request): JsonResponse
    {
        // Procesa el evento recibido y valida la firma de Stripe.
        $this->payments->handleWebhook(
            $request->getContent(),
            $request->headers->get('Stripe-Signature')
        );

        // Confirma la recepción correcta del evento.
        return $this->responses->success([
            'received' => true,
        ]);
    }
}
