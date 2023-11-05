<?php

namespace App\Psp;

use App\Message\Psp3PaymentMessage;

class Psp3 implements PaymentProviderInterface
{
    public function getName(): string
    {
        return 'psp3';
    }

    public function getMessageClassName(): string
    {
        return Psp3PaymentMessage::class;
    }
}
