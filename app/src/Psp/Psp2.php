<?php

namespace App\Psp;

use App\Message\Psp2PaymentMessage;

class Psp2 implements PaymentProviderInterface
{
    public function getName(): string
    {
        return 'psp2';
    }

    public function getMessageClassName(): string
    {
        return Psp2PaymentMessage::class;
    }
}
