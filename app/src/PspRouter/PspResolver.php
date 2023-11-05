<?php

namespace App\PspRouter;

use App\Entity\PaymentTransaction;
use App\PaymentTransaction\CardType;
use App\Psp\Psp1;
use App\Psp\Psp2;
use App\Psp\Psp3;

class PspResolver
{
    private array $container;

    public function __construct(
        private readonly PspMonetaryRouter   $monetaryRouter,
        private readonly PspRoundRobinRouter $roundRobinRouter,
        private readonly PspStraightRouter   $straightRouter,
        private readonly Psp1                $psp1,
        private readonly Psp2                $psp2,
        private readonly Psp3                $psp3,
    ) {
    }

    public function getRouter(PaymentTransaction $transaction): PspRouterInterface
    {
        $cardType = $transaction->getCreditCardTransactionParameters()->getType();
        $currency = $transaction->getCost()->getCurrency()->getCode();

        $key = $cardType->value . $currency;
        $router = $this->container[$key] ?? null;
        if ($router) {
            return $router;
        }

        // @TODO move to config
        if ($cardType === CardType::VISA->value and $currency === 'EUR') {
            $router = $this->straightRouter;
            $router->setPsp($this->psp1);
        } elseif ($cardType === CardType::MASTER_CARD->value and $currency === 'USD') {
            $router = $this->monetaryRouter;
            $router->setPsp([$this->psp1, $this->psp2]);
        } else {
            $router = $this->roundRobinRouter;
            $router->setPsp([$this->psp1, $this->psp2, $this->psp3]);
        }

        $this->container[$key] = $router;

        return $router;
    }
}
