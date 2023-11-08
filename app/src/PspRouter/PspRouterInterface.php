<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PspResponse;

interface PspRouterInterface
{
    public function route(PaymentTransaction $transaction): PspResponse;
}
