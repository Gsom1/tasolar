<?php

namespace App\Psp;

use App\Entity\PaymentTransaction;

interface PaymentProviderInterface
{
    public function payment(PaymentTransaction $transaction): PspResponse;
}
