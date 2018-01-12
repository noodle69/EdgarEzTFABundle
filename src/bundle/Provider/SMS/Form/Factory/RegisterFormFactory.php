<?php

namespace Edgar\EzTFABundle\Provider\SMS\Form\Factory;

use Edgar\EzTFABundle\Provider\SMS\Form\Data\SMSRegisterData;
use Edgar\EzTFABundle\Provider\SMS\Form\Type\RegisterType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterFormFactory
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
        SMSRegisterData $data,
        ?string $name = null
    ): ?FormInterface {
        $name = 'edgareztfasms';
        return $this->formFactory->createNamed(
            $name,
            RegisterType::class,
            $data,
            [
                'method' => Request::METHOD_POST,
                'csrf_protection' => true,
            ]
        );
    }
}
