<?php

// ─────────────────────────────────────────────────────────────────────────────
// InputValidator.php — validación de datos de entrada.
//
// Este servicio agrupa las validaciones más comunes de la aplicación para
// asegurar que los datos recibidos cumplen los requisitos antes de procesarlos.
// También valida valores permitidos para estados, roles y tipos definidos
// mediante enums.
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Service;

use App\Enum\LocalStatus;
use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Enum\PaymentMethod;
use App\Enum\ProductAvailability;
use App\Enum\Roles;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InputValidator
{
    public function __construct(private readonly ValidatorInterface $validator) {}

    // Comprueba que todos los campos obligatorios estén presentes.
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

    // Valida que el email tenga un formato correcto.
    public function email(string $email): void
    {
        if (count($this->validator->validate($email, [new Assert\NotBlank(), new Assert\Email()])) > 0) {
            throw new BadRequestHttpException('El email no es valido.');
        }
    }

    // Valida la longitud mínima requerida para la contraseña.
    public function password(string $password): void
    {
        if (count($this->validator->validate($password, [new Assert\NotBlank(), new Assert\Length(min: 8)])) > 0) {
            throw new BadRequestHttpException('La password debe tener al menos 8 caracteres.');
        }
    }

    // Comprueba que todos los roles pertenezcan a la lista permitida.
    public function roleList(array $roles): void
    {
        foreach ($roles as $role) {
            if (count($this->validator->validate($role, [new Assert\Choice(Roles::ALL)])) > 0) {
                throw new BadRequestHttpException('Rol no permitido: '.$role);
            }
        }
    }

    // Valida la disponibilidad de un producto.
    public function productAvailability(string $availability): void
    {
        if (count($this->validator->validate($availability, [new Assert\Choice(ProductAvailability::ALL)])) > 0) {
            throw new BadRequestHttpException('Disponibilidad de producto no permitida.');
        }
    }

    // Valida el estado asignado a un local.
    public function localStatus(string $status): void
    {
        if (count($this->validator->validate($status, [new Assert\Choice(LocalStatus::ALL)])) > 0) {
            throw new BadRequestHttpException('Estado de local no permitido.');
        }
    }

    // Valida el estado asignado a un pedido.
    public function orderStatus(string $status): void
    {
        if (count($this->validator->validate($status, [new Assert\Choice(OrderStatus::ALL)])) > 0) {
            throw new BadRequestHttpException('Estado de pedido no permitido.');
        }
    }

    // Valida el tipo de pedido recibido.
    public function orderType(string $type): void
    {
        if (count($this->validator->validate($type, [new Assert\Choice(OrderType::ALL)])) > 0) {
            throw new BadRequestHttpException('Tipo de pedido no permitido.');
        }
    }

    public function paymentMethod(string $method): void
    {
        if (count($this->validator->validate($method, [new Assert\Choice(PaymentMethod::ALL)])) > 0) {
            throw new BadRequestHttpException(json_encode([
                'paymentMethod' => ['Metodo de pago no permitido.'],
            ], JSON_THROW_ON_ERROR));
        }
    }
}
