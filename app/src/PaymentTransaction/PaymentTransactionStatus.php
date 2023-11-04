<?php

namespace App\PaymentTransaction;

enum PaymentTransactionStatus: int
{
    case APPROVED = 1;
    case FAILED = 11;
    case DECLINED = 12;
    case SETTLED = 10;
}
