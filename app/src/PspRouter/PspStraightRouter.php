<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PaymentProviderInterface;
use App\Psp\PspResponse;

class PspStraightRouter implements PspRouterInterface
{
    private PaymentProviderInterface $psp;

    public function __construct()
    {
    }

    public function route(PaymentTransaction $transaction): PspResponse
    {
        return $this->psp->payment($transaction);
    }

    public function setPsp(PaymentProviderInterface $psp): void
    {
        $this->psp = $psp;
    }
}
