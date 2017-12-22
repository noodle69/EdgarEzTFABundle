<?php

namespace Edgar\EzTFA\Provider;

use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

class AbstractProvider
{
    /** @var Session $session */
    protected $session;

    /** @var Translator $translator */
    protected $translator;

    /** @var RouterInterface */
    protected $router;

    /**
     * ProviderAbstract constructor.
     *
     * @param Session $session
     * @param Translator $translator
     */
    public function __construct(
        Session $session,
        Translator $translator,
        RouterInterface $router
    ) {
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Check if user is TFA authenticated
     *
     * @return mixed
     */
    public function isAuthenticated(): bool
    {
        return $this->session->get('tfa_authenticated', false);
    }

    /**
     * Return siteaccess host
     *
     * @param Request $request
     * @return string
     */
    protected function getSiteaccessUrl(Request $request): string
    {
        $semanticPathinfo = $request->attributes->get('semanticPathinfo') ?: '/';
        $semanticPathinfo = rtrim($semanticPathinfo, '/');
        $uri = $request->getUri();
        if (!$semanticPathinfo) {
            return $uri;
        }

        return substr($uri, 0, -strlen($semanticPathinfo));
    }

    public function register(
        EdgarEzTFARepository $tfaRepository,
        $userId, $provider
    ): string {
        $tfaRepository->setProvider($userId, $provider);
        return '';
    }

    public function getIdentifier(): ?string
    {
        return null;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function canBeMultiple(): bool
    {
        return false;
    }

    public function cancel(): void
    {
    }

    public function purge(APIUser $user): void
    {
    }
}
