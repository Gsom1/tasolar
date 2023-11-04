<?php

namespace App\Dto;

use Money\Money;

class NewPaymentDto
{
    public function __construct(
        public readonly string $merchantId,
        public readonly string $cardNumber,
        public readonly string $expiryDate,
        public readonly int    $cvv,
        public readonly Money  $amount,
    )
    {
    }
}
