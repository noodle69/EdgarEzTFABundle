<?php

namespace Edgar\EzTFABundle\Provider\U2F\Event;

use Edgar\EzTFABundle\Entity\EdgarEzTFA;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Edgar\EzTFA\Repository\EdgarEzTFAU2FRepository;
use Edgar\EzTFABundle\Entity\EdgarEzTFAU2F;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\RouterInterface;

class RegistrationListener
{
    /** @var EdgarEzTFAU2FRepository $tfaU2FRepository */
    protected $tfaU2FRepository;

    /** @var EdgarEzTFARepository $tfaRepository */
    protected $tfaRepository;

    /** @var RouterInterface $router */
    protected $router;

    /**
     * RegistrationListener constructor.
     *
     * @param Registry $doctrineRegistry
     * @param Router $router
     */
    public function __construct(
        Registry $doctrineRegistry,
        RouterInterface $router
    ) {
        $entityManager = $doctrineRegistry->getManager();
        $this->tfaU2FRepository = $entityManager->getRepository(EdgarEzTFAU2F::class);
        $this->tfaRepository = $entityManager->getRepository(EdgarEzTFA::class);

        $this->router = $router;
    }

    /**
     * onRegister
     *
     * @param RegisterEvent $event
     * @return void
     **/
    public function onRegister(RegisterEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser($event);
        $apiUser = $user->getAPIUser();

        $registrationData = $event->getRegistration();

        $this->tfaU2FRepository->saveKey($registrationData, $apiUser->id, $event->getKeyName());
        /** @var EdgarEzTFA $userProvider */
        $userProvider = $this->tfaRepository->findOneByUserId($apiUser->id);
        if ($userProvider && $userProvider->getProvider() !== 'u2f') {
            $this->tfaRepository->remove($userProvider);
        } else if (!$userProvider) {
            $this->tfaRepository->setProvider($apiUser->id, 'u2f');
        }

        $response = new RedirectResponse($this->router->generate('tfa_registered', ['provider' => 'u2f']));
        $event->setResponse($response);
    }
}
