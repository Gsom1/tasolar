<?php

namespace App\Services\ApproveService;

use App\Dto\NewPaymentDto;
use App\Entity\PaymentTransaction;
use App\PaymentTransaction\PaymentTransactionStatus;
use Psr\Log\LoggerInterface;

class ApproveService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function approve(NewPaymentDto $data, PaymentTransaction $paymentTransaction): void
    {
        $lastDigit = substr($data->cardNumber, -1);

        $status = $lastDigit % 2 === 0 ? PaymentTransactionStatus::APPROVED : PaymentTransactionStatus::DECLINED;

        $this->logger->info('Transaction status: ' . $status->name, [
            'data' => $data,
            'transactionId' => $paymentTransaction->getId(),
        ]);

        $paymentTransaction->setStatus($status);
    }
}
