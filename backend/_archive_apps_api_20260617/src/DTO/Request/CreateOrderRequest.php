<?php

namespace App\DTO\Request;

use App\Enum\OrderType;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrderRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(OrderType::ALL)]
    public string $type;

    public ?int $localId = null;
    public ?string $notes = null;
    public ?string $phone = null;
    public ?string $address = null;

    /** @var list<OrderLineRequest> */
    #[Assert\Count(min: 1)]
    public array $lines = [];
}
