<?php

namespace App\PaymentTransaction;

use App\Entity\CardBalance;
use App\Entity\PaymentTransaction;
use App\Money\MoneyFactory;
use App\Repository\CardBalanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;

class SettleProcess
{
    public function __construct(
        private readonly CardBalanceRepository  $cardBalanceRepository,
        private readonly MoneyFactory           $moneyFactory,
        private readonly LoggerInterface        $logger,
        private readonly EntityManagerInterface $em,
        private readonly LockFactory            $lockFactory
    ) {
    }

    public function settle(PaymentTransaction $transaction): void
    {
        $transactionParams = $transaction->getCreditCardTransactionParameters();
        $lock = $this->lockFactory->createLock('card_' . $transactionParams->getCardNumber());
        if (!$lock->acquire()) {
            throw new \RuntimeException("Unable to acquire lock");
        }

        try {
            $cardBalance = $this->cardBalanceRepository->findOneBy(
                [
                    CardBalance::FIELD_CARD_NUMBER => $transactionParams->getCardNumber(),
                ]
            );
            if (!$cardBalance) {
                //if there is no such card yet, give it 1000000 USD
                $cardBalance = new CardBalance();
                $cardBalance->setCardNumber($transactionParams->getCardNumber());
                $cardBalance->setBalance($this->moneyFactory->create(1000000, 'USD'));
            }

            /**
             * @TODO Convert currency
             */

            if ($cardBalance->getBalance()->lessThan($transaction->getCost())) {
                $transaction->setStatus(PaymentTransactionStatus::FAILED);
                $this->logger->warning('InsufficientFunds', [
                    'transaction' => $transaction,
                ]);
            } else {
                $transaction->setStatus(PaymentTransactionStatus::SETTLED);
                $newBalance = $cardBalance->getBalance()->subtract($transaction->getCost());
                $cardBalance->setBalance($newBalance);
            }

            $this->em->persist($cardBalance);
            $this->em->persist($transaction);
        } catch (\Throwable $e) {
            $lock->release();
            throw $e;
        }
    }
}
