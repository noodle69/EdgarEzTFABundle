<?php

namespace Edgar\EzTFABundle\Provider\Email\Form\Factory;

use Edgar\EzTFABundle\Provider\Email\Form\Data\EmailData;
use Edgar\EzTFABundle\Provider\Email\Form\Type\AuthType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactory
{
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function configure(
        EmailData $data,
        ?string $name = null
    ): ?FormInterface {
        $name = 'edgareztfaemail';
        return $this->formFactory->createNamed(
            $name,
            AuthType::class,
            $data,
            [
                'method' => Request::METHOD_POST,
                'csrf_protection' => true,
            ]
        );
    }
}
