<?php

namespace Edgar\EzTFABundle\Provider\U2F\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class AuthCode extends Constraint
{
    public function validatedBy()
    {
        return 'edgareztfa.u2f.auth.contraint';
    }
}
