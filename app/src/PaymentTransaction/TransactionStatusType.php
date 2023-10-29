<?php

namespace App\PaymentTransaction;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TransactionStatusType extends Type
{
    const NAME = 'transaction_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'INTEGER';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PaymentTransactionStatus
    {
        return $value !== null ? PaymentTransactionStatus::from((int)$value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        return $value ? $value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
