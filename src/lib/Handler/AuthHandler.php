<?php

namespace Edgar\EzTFA\Handler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFA\Provider\AbstractProvider;
use Edgar\EzTFA\Provider\ProviderInterface;
use Edgar\EzTFABundle\Entity\EdgarEzTFA;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\Translation\Translator;

class AuthHandler extends AbstractProvider implements ProviderInterface
{
    /** @var ProviderInterface[] $providers */
    private $providers = [];

    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var EdgarEzTFARepository $tfaRepository */
    protected $tfaRepository;

    /** @var RouterInterface */
    protected $router;

    protected $providersConfig;

    /**
     * AuthHandler constructor.
     *
     * @param TokenStorage $tokenStorage
     * @param Registry $doctrineRegistry
     */
    public function __construct(
        Session $session,
        Translator $translator,
        TokenStorage $tokenStorage,
        Registry $doctrineRegistry,
        RouterInterface $router,
        $providersConfig
    ) {
        parent::__construct($session, $translator, $router);
        $this->tokenStorage = $tokenStorage;

        $entityManager = $doctrineRegistry->getManager();
        $this->tfaRepository = $entityManager->getRepository(EdgarEzTFA::class);

        $this->providersConfig = $providersConfig;
    }

    /**
     * Register new TFA Provider
     *
     * @param ProviderInterface $provider
     * @param string $alias TFA Provider identifier
     */
    public function addProvider(ProviderInterface $provider, $alias)
    {
        if ((isset($this->providersConfig[$alias])
                && (!isset($this->providersConfig[$alias]['disabled'])
                    || (isset($this->providersConfig[$alias]['disabled'])
                        && $this->providersConfig[$alias]['disabled'] !== true)))
            || !isset($this->providersConfig[$alias])
        ) {
            $this->providers[$alias] = $provider;
        }
    }

    /**
     * List all TFA Providers
     *
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Check if user is TFA authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        $providerAlias = $this->getProviderAlias();
        if (!isset($this->providers[$providerAlias]))
            return true;

        return $this->providers[$providerAlias]->isAuthenticated();
    }

    /**
     * Ask for TFA Authentication
     *
     * @param Request $request
     * @return bool
     */
    public function requestAuthCode(Request $request): string
    {
        $providerAlias = $this->getProviderAlias();
        if (!isset($this->providers[$providerAlias]))
            return false;

        return $this->providers[$providerAlias]->requestAuthCode($request);
    }

    protected function getProviderAlias(): ?string
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if (!($user instanceof User)) {
            return null;
        }

        $apiUser = $user->getAPIUser();

        /** @var EdgarEzTFA $userProvider */
        $userProvider = $this->tfaRepository->findOneByUserId($apiUser->id);

        return ($userProvider) ? $userProvider->getProvider() : false;
    }
}
