<?php

namespace App\Service;

use App\Enum\LocalStatus;
use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Enum\ProductAvailability;
use App\Enum\Roles;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InputValidator
{
    public function __construct(private readonly ValidatorInterface $validator) {}

    public function requireFields(array $data, array $fields): void
    {
        $missing = [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === '' || $data[$field] === null) {
                $missing[$field] = 'Este campo es obligatorio.';
            }
        }

        if ($missing !== []) {
            throw new BadRequestHttpException(json_encode($missing, JSON_THROW_ON_ERROR));
        }
    }

    public function email(string $email): void
    {
        if (count($this->validator->validate($email, [new Assert\NotBlank(), new Assert\Email()])) > 0) {
            throw new BadRequestHttpException('El email no es valido.');
        }
    }

    public function password(string $password): void
    {
        if (count($this->validator->validate($password, [new Assert\NotBlank(), new Assert\Length(min: 8)])) > 0) {
            throw new BadRequestHttpException('La password debe tener al menos 8 caracteres.');
        }
    }

    public function roleList(array $roles): void
    {
        foreach ($roles as $role) {
            if (count($this->validator->validate($role, [new Assert\Choice(Roles::ALL)])) > 0) {
                throw new BadRequestHttpException('Rol no permitido: '.$role);
            }
        }
    }

    public function productAvailability(string $availability): void
    {
        if (count($this->validator->validate($availability, [new Assert\Choice(ProductAvailability::ALL)])) > 0) {
            throw new BadRequestHttpException('Disponibilidad de producto no permitida.');
        }
    }

    public function localStatus(string $status): void
    {
        if (count($this->validator->validate($status, [new Assert\Choice(LocalStatus::ALL)])) > 0) {
            throw new BadRequestHttpException('Estado de local no permitido.');
        }
    }

    public function orderStatus(string $status): void
    {
        if (count($this->validator->validate($status, [new Assert\Choice(OrderStatus::ALL)])) > 0) {
            throw new BadRequestHttpException('Estado de pedido no permitido.');
        }
    }

    public function orderType(string $type): void
    {
        if (count($this->validator->validate($type, [new Assert\Choice(OrderType::ALL)])) > 0) {
            throw new BadRequestHttpException('Tipo de pedido no permitido.');
        }
    }
}
