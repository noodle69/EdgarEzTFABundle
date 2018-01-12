<?php

namespace Edgar\EzTFABundle\Provider\SMS\Form\Data;

use libphonenumber\PhoneNumber;

class SMSRegisterData
{
    private $phone;

    public function __construct(?PhoneNumber $phone = null)
    {
        $this->phone = $phone;
    }

    public function setPhone(?PhoneNumber $phone = null): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

}
