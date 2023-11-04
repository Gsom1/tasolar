<?php

namespace App\Money;

use Money\Currencies\ISOCurrencies;
use Money\Money;
use Money\Currency;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

class MoneyFactory
{
    private readonly MoneyParser $parser;

    public function __construct()
    {
        //@TODO via IoC
        $this->parser = new DecimalMoneyParser(new ISOCurrencies());
    }

    public function create(float $amount, string $currency): Money
    {
        return $this->parser->parse((string)$amount, new Currency($currency));
    }
}
