<?php

namespace Edgar\EzTFABundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFABundle\Entity\EdgarEzTFA;
use Edgar\EzTFA\Provider\ProviderInterface;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Edgar\EzTFA\Handler\AuthHandler;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\Translator;

class TFAController extends Controller
{
    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var ConfigResolverInterface $configResolver */
    protected $configResolver;

    /** @var ProviderInterface[] $providers */
    protected $providers;

    /** @var AuthHandler $authHandler */
    protected $authHandler;

    /** @var EdgarEzTFARepository $tfaRepository */
    protected $tfaRepository;

    protected $providersConfig;

    /** @var NotificationHandlerInterface  */
    protected $notificationHandler;

    /** @var Translator  */
    protected $translator;

    /** @var RouterInterface  */
    protected $router;

    public function __construct(
        TokenStorage $tokenStorage,
        ConfigResolverInterface $configResolver,
        AuthHandler $authHandler,
        NotificationHandlerInterface $notificationHandler,
        Translator $translator,
        RouterInterface $router,
        Registry $doctrineRegistry,
        $providersConfig
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->configResolver = $configResolver;
        $this->authHandler = $authHandler;
        $this->providers = $this->authHandler->getProviders();
        $entityManager = $doctrineRegistry->getManager();
        $this->tfaRepository = $entityManager->getRepository(EdgarEzTFA::class);
        $this->providersConfig = $providersConfig;

        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function menuAction(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $providers = [];

        if ($user && $user instanceof User) {
            $apiUser = $user->getAPIUser();

            /** @var EdgarEzTFA $userProvider */
            $userProvider = $this->tfaRepository->findOneByUserId($apiUser->id);
            foreach ($this->providers as $provider) {
                $identifier = $provider->getIdentifier();
                if ((isset($this->providersConfig[$identifier])
                        && (!isset($this->providersConfig[$identifier]['disabled'])
                            || (isset($this->providersConfig[$identifier]['disabled'])
                                && $this->providersConfig[$identifier]['disabled'] !== true)))
                    || !isset($this->providersConfig[$identifier])
                ) {
                    $providers[$provider->getIdentifier()] = [
                        'selected'    => ($userProvider && $userProvider->getProvider() == $provider->getIdentifier()) ? true : false,
                        'title'       => $provider->getName(),
                        'description' => $provider->getDescription()
                    ];
                }
            }
        }

        return $this->render('EdgarEzTFABundle:profile:tfa/view.html.twig', [
            'providers' => $providers
        ]);
    }

    public function clickAction(string $provider): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        /** @var EdgarEzTFA $userProvider */
        $userProvider = $this->tfaRepository->findOneByUserId($apiUser->id);
        if (
            $userProvider &&
            (
                $userProvider->getProvider() != $provider ||
                (
                    $userProvider->getProvider() == $provider &&
                    !$this->providers[$userProvider->getProvider()]->canBeMultiple()
                )
            )
        ) {
            $this->providers[$userProvider->getProvider()]->purge($apiUser);
            $this->tfaRepository->remove($userProvider);
        }

        /** @var ProviderInterface[] $tfaProviders */
        $tfaProviders = $this->authHandler->getProviders();
        $tfaProvider = $tfaProviders[$provider];
        if ($redirect = $tfaProvider->register(
            $this->tfaRepository,
            $apiUser->id,
            $provider
        )) {
            return $this->redirect($redirect);
        }

        $this->notificationHandler->success(
            $this->translator->trans(
                'edgar.eztfa.provider.selected',
                [],
                'edgareztfa'
            )
        );

        return new RedirectResponse($this->router->generate('edgar.eztfa.menu'));
    }

    public function cancelAction(string $provider): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        /** @var EdgarEzTFA $userProvider */
        $userProvider = $this->tfaRepository->findOneByUserId($apiUser->id);
        if ($userProvider && $userProvider->getProvider() == $provider) {
            $this->tfaRepository->remove($userProvider);
            if (isset($this->providers[$provider])) {
                $this->providers[$provider]->cancel();
                $this->providers[$provider]->purge($apiUser);
            }
        }

        $this->notificationHandler->success(
            $this->translator->trans(
                'edgar.eztfa.provider.canceled',
                [],
                'edgareztfa'
            )
        );

        $redirectUrl = $this->generateUrl('edgar.eztfa.menu');
        return new RedirectResponse($redirectUrl);
    }

    public function registeredAction(string $provider): Response
    {
        $this->notificationHandler->success(
            $this->translator->trans(
                'edgar.eztfa.provider.registered',
                [],
                'edgareztfa'
            )
        );

        return new RedirectResponse($this->router->generate('edgar.eztfa.menu'));
    }

    public function reinitializeAction(string $provider): Response
    {
        $redirectUrl = $this->generateUrl('tfa_click', ['provider' => $provider]);
        return new RedirectResponse($redirectUrl);
    }
}
