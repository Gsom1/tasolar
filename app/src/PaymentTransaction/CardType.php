<?php

namespace App\PaymentTransaction;

enum CardType: string
{
    case VISA        = 'VISA';
    case MASTER_CARD = 'MC';
    case UNKNOWN     = 'UNKNOWN';
}
