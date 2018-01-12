<?php

namespace Edgar\EzTFABundle\Provider\Email\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class AuthCode extends Constraint
{
    public function validatedBy(): string
    {
        return AuthCodeValidator::class;
    }
}
