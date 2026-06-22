<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Enum\OrderStatus;
use App\Repository\LocalRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orders,
        private readonly ProductRepository $products,
        private readonly LocalRepository $locals,
        private readonly InputValidator $validator,
    ) {}

    public function create(User $user, array $data): CustomerOrder
    {
        $this->validator->requireFields($data, ['type', 'lines']);
        $this->validator->orderType((string) $data['type']);

        if (!is_array($data['lines']) || count($data['lines']) === 0) {
            throw new BadRequestHttpException('El pedido debe tener al menos una linea.');
        }

        $order = (new CustomerOrder())
            ->setReference($this->generateReference())
            ->setUser($user)
            ->setType((string) $data['type'])
            ->setNotes($data['notes'] ?? $data['notas'] ?? null)
            ->setPhone($data['phone'] ?? $data['telefono'] ?? $user->getPhone())
            ->setAddress($data['address'] ?? $data['direccion'] ?? null);

        $localId = $data['localId'] ?? $data['local_id'] ?? null;
        if (!empty($localId)) {
            $local = $this->locals->find((int) $localId);
            if ($local === null) {
                throw new BadRequestHttpException('Local no encontrado.');
            }
            $order->setLocal($local);
        }

        $total = 0.0;
        foreach ($data['lines'] as $lineData) {
            if (!is_array($lineData)) {
                throw new BadRequestHttpException('Linea de pedido no valida.');
            }
            $lineData['productId'] ??= $lineData['productoId'] ?? null;
            $lineData['quantity'] ??= $lineData['cantidad'] ?? null;
            $this->validator->requireFields($lineData, ['productId', 'quantity']);
            $quantity = (int) $lineData['quantity'];
            if ($quantity < 1) {
                throw new BadRequestHttpException('La cantidad minima es 1.');
            }

            $product = $this->products->find((int) $lineData['productId']);
            if ($product === null || !$product->isAvailable() || $product->getAvailability() === 'hidden') {
                throw new BadRequestHttpException('Producto no disponible.');
            }

            $line = (new OrderLine())
                ->setProduct($product)
                ->setQuantity($quantity)
                ->setUnitPrice($product->getPrice())
                ->setNotes($lineData['notes'] ?? null);
            $order->addLine($line);
            $total += ((float) $product->getPrice()) * $quantity;
        }

        $order->setTotal($total);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function getVisibleOrder(int $id, User $user, bool $canSeeAll): CustomerOrder
    {
        $order = $this->orders->find($id);
        if ($order === null) {
            throw new NotFoundHttpException('Pedido no encontrado.');
        }
        if (!$canSeeAll && $order->getUser()?->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('No puedes acceder a este pedido.');
        }

        return $order;
    }

    public function cancel(CustomerOrder $order, User $user, bool $canManageAll): CustomerOrder
    {
        if (!$canManageAll && $order->getUser()?->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('No puedes cancelar este pedido.');
        }

        $this->transition($order, OrderStatus::CANCELLED);
        return $order;
    }

    public function transition(CustomerOrder $order, string $targetStatus): CustomerOrder
    {
        $targetStatus = $this->normalizeStatus($targetStatus);
        $this->validator->orderStatus($targetStatus);
        $allowed = OrderStatus::TRANSITIONS[$order->getStatus()] ?? [];
        if (!in_array($targetStatus, $allowed, true)) {
            throw new BadRequestHttpException(sprintf('Transicion de %s a %s no permitida.', $order->getStatus(), $targetStatus));
        }

        $order->setStatus($targetStatus);
        $this->entityManager->flush();

        return $order;
    }

    private function normalizeStatus(string $status): string
    {
        return [
            'pendiente' => OrderStatus::PENDING,
            'confirmado' => OrderStatus::CONFIRMED,
            'preparando' => OrderStatus::PREPARING,
            'listo' => OrderStatus::READY,
            'entregado' => OrderStatus::COMPLETED,
            'cancelado' => OrderStatus::CANCELLED,
        ][mb_strtolower(trim($status))] ?? $status;
    }

    private function generateReference(): string
    {
        do {
            $reference = 'BAME-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
        } while ($this->orders->findOneBy(['reference' => $reference]) !== null);

        return $reference;
    }
}
