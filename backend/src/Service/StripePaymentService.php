<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\User;

final class StripePaymentService
{
    public function __construct(
        private readonly CheckoutSessionService $checkoutSessions,
        private readonly StripeWebhookHandler $webhooks,
    ) {}

    /**
     * @return array{order: CustomerOrder, paymentMethod: string, paymentStatus: string, requiresOnlinePayment: bool, checkoutUrl?: string}
     */
    public function createCheckoutSession(User $user, array $data, ?string $providedIdempotencyKey = null): array
    {
        return $this->checkoutSessions->createCheckoutSession($user, $data, $providedIdempotencyKey);
    }

    public function handleWebhook(string $payload, ?string $signature): void
    {
        $this->webhooks->handle($payload, $signature);
    }
}
