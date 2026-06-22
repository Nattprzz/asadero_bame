<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\Roles;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AdminLocalScopeResolver
{
    public function resolve(User $user, mixed $requestedLocalId): ?int
    {
        if ($this->isAdmin($user)) {
            return $requestedLocalId === null || $requestedLocalId === ''
                ? null
                : (int) $requestedLocalId;
        }

        $userLocalId = $user->getLocal()?->getId();

        if ($userLocalId === null) {
            throw new AccessDeniedHttpException('Usuario sin local asignado.');
        }

        if ($requestedLocalId !== null && $requestedLocalId !== '' && (int) $requestedLocalId !== $userLocalId) {
            throw new AccessDeniedHttpException('No puedes acceder a datos de otro local.');
        }

        return $userLocalId;
    }

    private function isAdmin(User $user): bool
    {
        return in_array(Roles::ADMIN, $user->getRoles(), true);
    }
}
