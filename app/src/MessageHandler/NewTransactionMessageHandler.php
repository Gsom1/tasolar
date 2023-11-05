<?php

namespace App\MessageHandler;

use App\Message\NewPaymentTransactionMessage;
use App\PspRouter\PspResolver;
use App\Repository\PaymentTransactionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewTransactionMessageHandler
{
    public function __construct(
        private readonly PspResolver                  $pspResolver,
        private readonly PaymentTransactionRepository $transactionRepository,
        private readonly LoggerInterface              $logger,
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

        $router = $this->pspResolver->getRouter($transaction);

        $router->route($transaction);
    }
}
