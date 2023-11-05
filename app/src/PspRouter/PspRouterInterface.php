<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;

interface PspRouterInterface
{
    public function route(PaymentTransaction $transaction);
}
