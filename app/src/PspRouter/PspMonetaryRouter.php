<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PaymentProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @TODO in case when run multiple workers, keep $pspProcessedAmount in Redis
 */
class PspMonetaryRouter implements PspRouterInterface
{
    private array $pspProcessedAmount;

    /**
     * @var PaymentProviderInterface[]
     */
    private array $psp;

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function route(PaymentTransaction $transaction)
    {
        if (count($this->psp) < 1) {
            throw new \Exception('NoPaymentProviders');
        }

        $providerKey = $this->getProviderKey();
        $psp = $this->psp[$providerKey];
        $messageClass = $psp->getMessageClassName();
        $transactionAmount = (int)$transaction->getCost()->getAmount();
        $this->pspProcessedAmount[$providerKey] += $transactionAmount;

        $this->bus->dispatch(new $messageClass($transaction->getId()));
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
