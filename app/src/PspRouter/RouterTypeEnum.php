<?php

namespace App\PspRouter;

enum RouterTypeEnum
{
    case Straight;
    case RoundRobin;
    case Monetary;
}
