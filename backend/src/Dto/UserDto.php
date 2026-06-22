<?php

namespace App\Dto;

use App\Entity\User;

final class UserDto
{
    public static function fromEntity(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'roles' => $user->getRoles(),
            'localId' => $user->getLocal()?->getId(),
            'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $user->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
