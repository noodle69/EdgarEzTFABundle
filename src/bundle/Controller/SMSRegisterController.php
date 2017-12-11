<?php

namespace Edgar\EzTFABundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFABundle\Entity\EdgarEzTFA;
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

    /**
     * RegisterController constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param TokenStorage $tokenStorage
     * @param Registry $doctrineRegistry
     * @param ProviderInterface $provider
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        TokenStorage $tokenStorage,
        Registry $doctrineRegistry,
        ProviderInterface $provider
    ) {
        $this->configResolver = $configResolver;
        $this->tokenStorage = $tokenStorage;

        $entityManager = $doctrineRegistry->getManager();
        $this->tfaRepository = $entityManager->getRepository(EdgarEzTFA::class);
        $this->tfaSMSRepository = $entityManager->getRepository(EdgarEzTFASMS::class);

        $this->provider = $provider;
    }

    /**
     * Ask user form phone number and register TFA Provider configuration
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $actionUrl = $this->generateUrl('tfa_sms_register_form');
        $redirectUrl = $this->generateUrl('tfa_registered', ['provider' => $this->provider->getIdentifier()]);

        $TFASMS = new EdgarEzTFASMS();
        $form = $this->createForm('Edgar\EzTFABundle\Provider\SMS\Form\Type\RegisterType', $TFASMS);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PhoneNumber $phoneObject */
            $phoneObject = $TFASMS->getPhone();
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
        }

        return $this->render('EdgarEzTFABundle:profile:tfa/sms/register.html.twig', [
            'layout' => $this->configResolver->getParameter('pagelayout'),
            'form' => $form->createView(),
            'actionUrl' => $actionUrl
        ]);
    }
}
