<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CardExpiryDate extends Constraint
{
    public $message = 'The expiry date "{{ string }}" is not valid.';

    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
