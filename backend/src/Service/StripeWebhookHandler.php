<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\Local;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class StripeWebhookHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orders,
        private readonly OrderStockService $stockService,
        private readonly StripeConfigValidator $configValidator,
        private readonly StripeEventLedgerService $ledger,
        private readonly LoggerInterface $logger,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%env(STRIPE_WEBHOOK_SECRET)%')]
        private readonly string $stripeWebhookSecret,
    ) {}

    public function handle(string $payload, ?string $signature): void
    {
        $this->configValidator->assertWebhookSecret($this->stripeWebhookSecret);
        $ledger = null;
        $eventId = null;

        if ($signature === null || $signature === '') {
            throw new UnauthorizedHttpException('Stripe', 'Falta la firma del webhook.');
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $this->stripeWebhookSecret);
        } catch (SignatureVerificationException|\UnexpectedValueException) {
            throw new UnauthorizedHttpException('Stripe', 'Firma de Stripe no valida.');
        }

        if (!is_string($event->id) || trim($event->id) === '') {
            throw new BadRequestHttpException('El evento de Stripe no es valido.');
        }

        $eventId = $event->id;

        if (!in_array($event->type, ['checkout.session.completed', 'checkout.session.expired'], true)) {
            $this->logger->info('Stripe webhook ignored.', [
                'event_id' => $event->id,
                'type' => $event->type,
            ]);

            return;
        }

        /** @var Session $session */
        $session = $event->data->object;
        $sessionId = (string) ($session->id ?? '');
        $orderId = isset($session->metadata->order_id) ? (int) $session->metadata->order_id : 0;

        if ($sessionId === '') {
            throw new BadRequestHttpException('La sesion de Stripe no es valida.');
        }

        $ledger = $this->ledger->registerIncoming($eventId, $event->type, json_decode($payload, true, flags: JSON_THROW_ON_ERROR));

        if ($ledger->isProcessed()) {
            $this->logger->info('Stripe webhook duplicate ignored.', [
                'event_id' => $eventId,
                'type' => $event->type,
            ]);

            return;
        }

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $order = $this->findLockedStripeOrder($orderId, $sessionId);
            $this->assertSessionBelongsToOrder($session, $order, $orderId, $sessionId);

            if ($event->type === 'checkout.session.expired') {
                $this->releaseExpiredReservation($order);
                $this->entityManager->flush();
                $this->ledger->markProcessed($ledger);
                $connection->commit();
                $this->logger->info('Stripe checkout session expired.', [
                    'event_id' => $eventId,
                    'order_id' => $order->getId(),
                    'session_id' => $sessionId,
                ]);

                return;
            }

            $this->assertCompletedSessionMatchesOrder($session, $order);

            if ($order->getPaymentStatus() === PaymentStatus::PAID) {
                $connection->commit();
                $this->ledger->markProcessed($ledger);
                $this->logger->info('Stripe checkout completed duplicate ignored.', [
                    'event_id' => $eventId,
                    'order_id' => $order->getId(),
                    'session_id' => $sessionId,
                ]);

                return;
            }

            if (
                $order->getPaymentStatus() !== PaymentStatus::PENDING
                || $order->getStatus() !== OrderStatus::PENDING
            ) {
                throw new BadRequestHttpException('El pedido no esta pendiente de confirmacion.');
            }

            $order
                ->setPaymentStatus(PaymentStatus::PAID)
                ->setPaidAt(new \DateTimeImmutable());

            $order->setStatus(OrderStatus::CONFIRMED);

            $this->entityManager->flush();
            $this->ledger->markProcessed($ledger);
            $connection->commit();

            $this->logger->info('Stripe payment confirmed.', [
                'event_id' => $eventId,
                'order_id' => $order->getId(),
                'session_id' => $sessionId,
            ]);
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            if ($ledger instanceof \App\Entity\StripeEventLedger) {
                try {
                    $this->ledger->markFailed($ledger, $exception->getMessage());
                } catch (\Throwable $ledgerException) {
                    $this->logger->warning('Stripe webhook ledger could not be marked as failed.', [
                        'event_id' => $eventId,
                        'ledger_exception' => get_class($ledgerException),
                    ]);
                }
            }

            $this->entityManager->clear();

            $this->logger->error('Stripe webhook processing failed.', [
                'event_id' => $eventId,
                'exception' => get_class($exception),
            ]);

            throw $exception;
        }
    }

    private function assertSessionBelongsToOrder(
        Session $session,
        CustomerOrder $order,
        int $metadataOrderId,
        string $sessionId
    ): void {
        if ($order->getStripeCheckoutSessionId() !== $sessionId) {
            throw new BadRequestHttpException('La sesion de Stripe no corresponde al pedido.');
        }

        if ($metadataOrderId < 1 || $order->getId() !== $metadataOrderId) {
            throw new BadRequestHttpException('Los metadatos de Stripe no corresponden al pedido.');
        }
    }

    private function assertCompletedSessionMatchesOrder(Session $session, CustomerOrder $order): void
    {
        if (($session->payment_status ?? null) !== 'paid') {
            throw new BadRequestHttpException('Stripe no ha confirmado el pago de la sesion.');
        }

        if (($session->mode ?? null) !== 'payment') {
            throw new BadRequestHttpException('El modo de la sesion de Stripe no es valido.');
        }

        if (mb_strtolower((string) ($session->currency ?? '')) !== 'eur') {
            throw new BadRequestHttpException('La moneda de la sesion de Stripe no coincide con el pedido.');
        }

        $expectedAmount = (int) round(((float) $order->getTotal()) * 100);

        if (!is_int($session->amount_total ?? null) || $session->amount_total !== $expectedAmount) {
            throw new BadRequestHttpException('El importe de la sesion de Stripe no coincide con el pedido.');
        }
    }

    private function findLockedStripeOrder(int $orderId, string $sessionId): CustomerOrder
    {
        $order = $orderId > 0 ? $this->orders->findForUpdate($orderId) : null;

        if ($order === null) {
            $unlockedOrder = $this->orders->findOneBy(['stripeCheckoutSessionId' => $sessionId]);
            $order = $unlockedOrder?->getId() !== null
                ? $this->orders->findForUpdate($unlockedOrder->getId())
                : null;
        }

        if (!$order instanceof CustomerOrder) {
            throw new BadRequestHttpException('Pedido asociado a Stripe no encontrado.');
        }

        return $order;
    }

    private function releaseExpiredReservation(CustomerOrder $order): void
    {
        if ($order->getPaymentStatus() === PaymentStatus::PAID || $order->getStatus() === OrderStatus::CANCELLED) {
            return;
        }

        if ($order->getPaymentStatus() !== PaymentStatus::PENDING || $order->getStatus() !== OrderStatus::PENDING) {
            throw new BadRequestHttpException('El pedido ya no esta pendiente de pago.');
        }

        $local = $order->getLocal();

        if (!$local instanceof Local) {
            throw new BadRequestHttpException('El pedido no tiene local asociado.');
        }

        $this->stockService->restore($order);

        $order
            ->setStatus(OrderStatus::CANCELLED)
            ->setPaymentStatus(PaymentStatus::FAILED);
    }
}
