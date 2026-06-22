<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterRequest
{
    #[Assert\NotBlank]
    public string $name;

    public string $surname = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    public ?string $phone = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}
