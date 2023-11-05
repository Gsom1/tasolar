<?php

namespace App\Psp;

use App\Message\Psp1PaymentMessage;

class Psp1 implements PaymentProviderInterface
{
    public function getName(): string
    {
        return 'psp1';
    }

    public function getMessageClassName(): string
    {
        return Psp1PaymentMessage::class;
    }
}
