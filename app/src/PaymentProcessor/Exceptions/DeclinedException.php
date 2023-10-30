<?php

namespace App\PaymentProcessor\Exceptions;

class DeclinedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('declined', 422);
    }
}
