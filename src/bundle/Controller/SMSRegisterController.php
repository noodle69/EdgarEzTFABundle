<?php

namespace Edgar\EzTFABundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFABundle\Entity\EdgarEzTFA;
use Edgar\EzTFABundle\Provider\SMS\Form\Data\SMSRegisterData;
use Edgar\EzTFABundle\Provider\SMS\Form\Factory\RegisterFormFactory;
use Edgar\EzTFABundle\Provider\SMS\Form\SubmitHandler;
use Edgar\EzTFABundle\Provider\SMS\Form\Type\RegisterType;
use libphonenumber\PhoneNumber;
use Edgar\EzTFABundle\Entity\EdgarEzTFASMS;
use Edgar\EzTFA\Provider\ProviderInterface;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Edgar\EzTFA\Repository\EdgarEzTFASMSRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SMSRegisterController extends Controller
{
    /** @var ConfigResolverInterface $configResolver */
    protected $configResolver;

    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var EdgarEzTFARepository $tfaRepository */
    protected $tfaRepository;

    /** @var EdgarEzTFASMSRepository $tfaSMSRepository  */
    protected $tfaSMSRepository;

    /** @var ProviderInterface $provider */
    protected $provider;

    /** @var RegisterFormFactory  */
    protected $registerFormFactory;

    /** @var SubmitHandler  */
    protected $submitHandler;

    public function __construct(
        ConfigResolverInterface $configResolver,
        TokenStorage $tokenStorage,
        Registry $doctrineRegistry,
        ProviderInterface $provider,
        RegisterFormFactory $registerFormFactory,
        SubmitHandler $submitHandler
    ) {
        $this->configResolver = $configResolver;
        $this->tokenStorage = $tokenStorage;

        $entityManager = $doctrineRegistry->getManager();
        $this->tfaRepository = $entityManager->getRepository(EdgarEzTFA::class);
        $this->tfaSMSRepository = $entityManager->getRepository(EdgarEzTFASMS::class);

        $this->provider = $provider;
        $this->registerFormFactory = $registerFormFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * Ask user form phone number and register TFA Provider configuration
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $actionUrl = $this->generateUrl('tfa_sms_register_form');

        $registerType = $this->registerFormFactory->configure(
            new SMSRegisterData()
        );
        $registerType->handleRequest($request);

        if ($registerType->isSubmitted()) {
            $result = $this->submitHandler->handle($registerType, function (SMSRegisterData $data) use ($registerType) {
                $redirectUrl = $this->generateUrl('tfa_registered', ['provider' => $this->provider->getIdentifier()]);
                $phoneObject = $data->getPhone();
                $phoneNumber = '+' . $phoneObject->getCountryCode() . $phoneObject->getNationalNumber();

                /** @var User $user */
                $user = $this->tokenStorage->getToken()->getUser();
                $apiUser = $user->getAPIUser();

                /** @var EdgarEzTFASMS $userSMS */
                $userSMS = $this->tfaSMSRepository->findOneByUserId($apiUser->id);
                if ($userSMS) {
                    $this->tfaSMSRepository->remove($userSMS);
                }
                $this->tfaSMSRepository->savePhone($apiUser->id, $phoneNumber);
                $this->tfaRepository->setProvider($apiUser->id, $this->provider->getIdentifier());

                return new RedirectResponse($redirectUrl);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('EdgarEzTFABundle:profile:tfa/sms/register.html.twig', [
            'form' => $registerType->createView(),
            'actionUrl' => $actionUrl
        ]);
    }
}
