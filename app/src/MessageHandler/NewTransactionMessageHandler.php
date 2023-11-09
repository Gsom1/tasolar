<?php

namespace App\MessageHandler;

use App\Entity\CardBalance;
use App\Message\NewPaymentTransactionMessage;
use App\Money\MoneyFactory;
use App\PaymentTransaction\PaymentTransactionStatus;
use App\PaymentTransaction\SettleProcess;
use App\Repository\CardBalanceRepository;
use App\Repository\PaymentTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewTransactionMessageHandler
{
    public function __construct(
        private readonly PaymentTransactionRepository $transactionRepository,
        private readonly LoggerInterface              $logger,
        private readonly EntityManagerInterface       $em,
        private readonly SettleProcess                $settleProcess,
    ) {
    }

    public function __invoke(NewPaymentTransactionMessage $message): void
    {
        $this->logger->debug(__CLASS__, [$message]);

        $transaction = $this->transactionRepository->find($message->transactionId);
        if (!$transaction) {
            $this->logger->warning('TransactionNotFound', ['transactionId' => $message->transactionId]);
            return;
        }

        $this->settleProcess->settle($transaction);

        $this->em->flush();
    }
}
