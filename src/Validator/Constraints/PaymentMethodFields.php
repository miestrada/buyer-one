<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PaymentMethodFields extends Constraint
{
    public string $message = 'This {{ test }} is a test message';

}