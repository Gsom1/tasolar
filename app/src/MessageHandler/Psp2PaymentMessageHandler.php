<?php

namespace App\MessageHandler;

use App\Message\Psp2PaymentMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Psp2PaymentMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Psp2PaymentMessage $message): void
    {
        $this->logger->debug(__CLASS__, [$message]);
    }
}
