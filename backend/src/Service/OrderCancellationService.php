<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OrderCancellationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orders,
        private readonly OrderAuthorizationService $authorization,
        private readonly OrderStockService $stockService,
        private readonly LoggerInterface $logger,
    ) {}

    public function cancel(int $id, User $user): CustomerOrder
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $order = $this->orders->findForUpdate($id);

            if ($order === null) {
                throw new NotFoundHttpException('Pedido no encontrado.');
            }

            $this->authorization->assertCanAccessOrder($order, $user, 'No puedes cancelar este pedido.');

            if ($order->getStatus() === OrderStatus::CANCELLED) {
                if ($order->getPaymentStatus() !== PaymentStatus::PAID && $order->getPaymentStatus() !== PaymentStatus::FAILED) {
                    $order->setPaymentStatus(PaymentStatus::FAILED);
                    $this->entityManager->flush();
                }

                $connection->commit();
                $this->logger->info('Order cancellation reused idempotently.', [
                    'order_id' => $order->getId(),
                    'payment_status' => $order->getPaymentStatus(),
                ]);

                return $order;
            }

            if (!in_array($order->getStatus(), [OrderStatus::PENDING, OrderStatus::CONFIRMED], true)) {
                throw new UnprocessableEntityHttpException(
                    sprintf('No se puede cancelar un pedido en estado %s.', $order->getStatus())
                );
            }

            $this->stockService->restore($order);

            $order->setStatus(OrderStatus::CANCELLED);

            if ($order->getPaymentStatus() !== PaymentStatus::PAID) {
                $order->setPaymentStatus(PaymentStatus::FAILED);
            }

            $this->entityManager->flush();
            $connection->commit();
            $this->logger->info('Order cancelled and stock restored.', [
                'order_id' => $order->getId(),
                'payment_status' => $order->getPaymentStatus(),
            ]);

            return $order;
        } catch (\Throwable $exception) {
            $connection->rollBack();
            $this->entityManager->clear();

            throw $exception;
        }
    }
}
