<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PaymentProviderInterface;
use App\Psp\PspResponse;

class PspMonetaryRouter implements PspRouterInterface
{
    private array $pspProcessedAmount;

    /**
     * @var PaymentProviderInterface[]
     */
    private array $psp;

    public function __construct(
    ) {
    }

    public function route(PaymentTransaction $transaction): PspResponse
    {
        if (count($this->psp) < 1) {
            throw new \Exception('NoPaymentProviders');
        }

        $providerKey = $this->getProviderKey();
        $psp = $this->psp[$providerKey];
        $transactionAmount = (int)$transaction->getCost()->getAmount();
        $this->pspProcessedAmount[$providerKey] += $transactionAmount;

        return $psp->payment($transaction);
    }

    private function getProviderKey(): int
    {
        $minValue = min($this->pspProcessedAmount);

        return array_search($minValue, $this->pspProcessedAmount);
    }

    public function setPsp(array $psp): void
    {
        $this->psp = $psp;
    }
}
