<?php

namespace App\Psp;

class PspResponse
{
    public function __construct(private readonly bool $approved, private readonly ?string $payload = null)
    {
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }
}
