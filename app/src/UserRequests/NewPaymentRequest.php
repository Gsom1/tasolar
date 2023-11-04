<?php

namespace App\UserRequests;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validation as CustomAssert;

class NewPaymentRequest
{
    #[Assert\NotBlank]
    #[Assert\Luhn]
    public string $cardNumber;

    #[Assert\Sequentially([
        new Assert\NotBlank(),
        new Assert\Regex('~(0[1-9]|1[0-2])/?([0-9]{4}|[0-9]{2})$~'),
        new CustomAssert\CardExpiryDate(),
    ])]
    public string $expiryDate;

    #[Assert\NotBlank]
    #[Assert\Range(min: 100, max: 999)]
    public int $cvv;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Currency]
    public string $currency;

    #[Assert\NotBlank]
    public string $merchantId;
}
