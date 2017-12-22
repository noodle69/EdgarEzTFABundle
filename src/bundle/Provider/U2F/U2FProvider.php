<?php

namespace Edgar\EzTFABundle\Provider\U2F;

use Doctrine\Bundle\DoctrineBundle\Registry;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Edgar\EzTFABundle\Entity\EdgarEzTFAU2F;
use Edgar\EzTFA\Provider\AbstractProvider;
use Edgar\EzTFA\Provider\ProviderInterface;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Edgar\EzTFA\Repository\EdgarEzTFAU2FRepository;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\RouterInterface;

class U2FProvider extends AbstractProvider implements ProviderInterface
{
    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var EdgarEzTFAU2FRepository $tfaU2FRepository */
    protected $tfaU2FRepository;

    /**
     * U2FProvider constructor.
     *
     * @param Router $router
     * @param Session $session
     * @param Translator $translator
     */
    public function __construct(
        Session $session,
        Translator $translator,
        TokenStorage $tokenStorage,
        RouterInterface $router,
        Registry $doctrineRegistry
    ) {
        parent::__construct($session, $translator, $router);
        $this->tokenStorage = $tokenStorage;

        $entityManager = $doctrineRegistry->getManager();
        /** @var EdgarEzTFAU2FRepository tfaU2FRepository */
        $this->tfaU2FRepository = $entityManager->getRepository(EdgarEzTFAU2F::class);
    }

    /**
     * Return url to request auth code
     *
     * @param Request $request
     * @return string
     */
    public function requestAuthCode(Request $request): string
    {
        $this->session->set('tfa_redirecturi', $request->getUri());

        return $this->router->generate('tfa_u2f_auth_form');
    }

    /**
     * Redirect user to register view
     *
     * @param EdgarEzTFARepository $tfaRepository
     * @param $userId
     * @param $provider
     * @return string
     */
    public function register(
        EdgarEzTFARepository $tfaRepository,
        $userId, $provider
    ): string {
        return $this->router->generate('tfa_u2f_register_form');
    }

    public function getIdentifier(): ?string
    {
        return 'u2f';
    }

    public function getName(): ?string
    {
        return $this->translator->trans('u2f.provider.name', [], 'edgareztfa');
    }

    public function getDescription(): ?string
    {
        return $this->translator->trans('u2f.provider.description', [], 'edgareztfa');
    }

    public function canBeMultiple(): bool
    {
        return true;
    }

    public function cancel(): void
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        /** @var EdgarEzTFAU2F[] $u2fKeys */
        $u2fKeys = $this->tfaU2FRepository->findByUserId($apiUser->id);
        if ($u2fKeys) {
            foreach ($u2fKeys as $u2fKey) {
                $this->tfaU2FRepository->remove($u2fKey);
            }
        }
    }

    public function purge(APIUser $user): void
    {
        $this->tfaU2FRepository->purge($user);
    }
}
