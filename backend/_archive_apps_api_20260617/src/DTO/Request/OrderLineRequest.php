<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderLineRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $productId;

    #[Assert\Positive]
    public int $quantity = 1;

    public ?string $notes = null;
}
