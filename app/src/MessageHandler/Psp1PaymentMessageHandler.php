<?php

namespace App\MessageHandler;

use App\Message\Psp1PaymentMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Psp1PaymentMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Psp1PaymentMessage $message): void
    {
        $this->logger->debug(__CLASS__, [$message]);
    }
}
