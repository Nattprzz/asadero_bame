<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\User;
use App\Enum\Roles;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class OrderAuthorizationService
{
    public function isAdmin(User $user): bool
    {
        return in_array(Roles::ADMIN, $user->getRoles(), true);
    }

    public function isLocalOperator(User $user): bool
    {
        return in_array(Roles::RESPONSABLE, $user->getRoles(), true)
            || in_array(Roles::GERENTE, $user->getRoles(), true);
    }

    public function localId(User $user): ?int
    {
        return $user->getLocal()?->getId();
    }

    public function assertCanAccessOrder(CustomerOrder $order, User $user, string $message): void
    {
        if ($this->isAdmin($user)) {
            return;
        }

        if ($this->isLocalOperator($user)) {
            $userLocalId = $this->localId($user);

            if ($userLocalId !== null && $order->getLocal()?->getId() === $userLocalId) {
                return;
            }

            throw new AccessDeniedHttpException($message);
        }

        if ($order->getUser()?->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException($message);
        }
    }
}
