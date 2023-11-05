<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PaymentProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PspStraightRouter implements PspRouterInterface
{
    private string $messageClass;
    private PaymentProviderInterface $psp;

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function route(PaymentTransaction $transaction)
    {
        $this->bus->dispatch(new $this->messageClass($transaction->getId()));
    }

    public function setPsp(PaymentProviderInterface $psp): void
    {
        $this->psp = $psp;
        $this->messageClass = $this->psp->getMessageClassName();
    }
}
