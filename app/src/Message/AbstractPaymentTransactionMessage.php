<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

abstract class AbstractPaymentTransactionMessage
{
    public function __construct(public readonly Uuid $transactionId)
    {
    }
}
