<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\PaymentProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PspRoundRobinRouter implements PspRouterInterface
{
    private int $counter = 0;
    private int $max;

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

        $psp = $this->getProvider();
        $messageClass = $psp->getMessageClassName();
        $this->bus->dispatch(new $messageClass($transaction->getId()));
    }

    private function getProvider(): PaymentProviderInterface
    {
        $psp = $this->psp[$this->counter];

        if ($this->counter >= $this->max) {
            $this->counter = 0;
        } else {
            $this->counter++;
        }

        return $psp;
    }

    public function setPsp(array $psp): void
    {
        $this->max = count($psp) - 1;
        $this->psp = $psp;
    }
}
