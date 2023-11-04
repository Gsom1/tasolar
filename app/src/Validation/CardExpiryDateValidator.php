<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CardExpiryDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CardExpiryDate) {
            throw new UnexpectedTypeException($constraint, CardExpiryDate::class);
        }
        if (!is_string($value)) {
            throw new \UnexpectedValueException($value, 'string');
        }
        $result = false;

        $yearLength = strlen(explode('/', $value)[1]);
        $format = match($yearLength) {
            2 => 'm/y',
            4 => 'm/Y'
        };

        $valueDate = \DateTime::createFromFormat($format, $value);
        if ($valueDate) {
            $result = true;
            $valueDate->modify('last day of');
            $valueDate->setTime(23, 59, 59);
        }

        if (!$result || $valueDate < new \DateTime()) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ string }}', $value)
                          ->addViolation();
        }
    }
}
