<?php

namespace App\DTO\Response;

final readonly class ApiEnvelope
{
    public function __construct(
        public mixed $data,
        public int $status,
    ) {}
}
