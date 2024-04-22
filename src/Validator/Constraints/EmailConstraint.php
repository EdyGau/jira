<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute] class EmailConstraint extends Constraint
{
    public string $message = 'The email "{{ value }}" is not a valid email.';
}
