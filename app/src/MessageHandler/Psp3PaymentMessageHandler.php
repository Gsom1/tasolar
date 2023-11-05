<?php

namespace App\MessageHandler;

use App\Message\Psp3PaymentMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Psp3PaymentMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(Psp3PaymentMessage $message): void
    {
        $this->logger->debug(__CLASS__, [$message]);
    }
}
