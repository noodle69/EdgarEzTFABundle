<?php

namespace Edgar\EzTFABundle\Provider\SMS\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class AuthCode extends Constraint
{
    public function validatedBy()
    {
        return 'edgareztfa.sms.auth.contraint';
    }
}
