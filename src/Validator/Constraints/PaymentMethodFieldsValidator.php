<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PaymentMethodFieldsValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PaymentMethodFields) {
            throw new UnexpectedTypeException($constraint, PaymentMethodFields::class);
        }


        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ test }}', 'hi')
            ->addViolation();
    }

}