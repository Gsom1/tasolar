<?php

namespace App\Psp;

interface PaymentProviderInterface
{
    public function getName(): string;

    public function getMessageClassName(): string;
}
