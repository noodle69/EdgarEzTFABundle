<?php

namespace Edgar\EzTFABundle\Provider\Email\Form\Data;

class EmailData
{
    private $code;

    public function __construct(?int $code = null)
    {
        $this->code = $code;
    }

    public function setCode(?int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }
}
