<?php

namespace App\UserRequests;

use Symfony\Component\Validator\Constraints as Assert;

class NewPaymentRequest
{
    #[Assert\NotBlank]
    #[Assert\Luhn]
    public string $cardNumber;

    #[Assert\NotBlank]
//    #[Assert\Date]
    public string $expiryDate;

    #[Assert\NotBlank]
    #[Assert\Range(min: 100, max: 999)]
    public int $cvv;

    #[Assert\NotBlank]
    public float $amount;

    #[Assert\NotBlank]
    public string $currency;

    #[Assert\NotBlank]
    public string $merchantId;
}
