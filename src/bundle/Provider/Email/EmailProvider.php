<?php

namespace Edgar\EzTFABundle\Provider\Email;

use Edgar\EzTFA\Provider\AbstractProvider;
use Edgar\EzTFA\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class EmailProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Return url to request auth code
     *
     * @param Request $request
     * @return string
     */
    public function requestAuthCode(Request $request): string
    {
        $authCode = random_int(100000, 999999);
        $this->session->set('tfa_authcode', $authCode);
        $this->session->set('tfa_redirecturi', $request->getUri());

        $redirectUrl = $this->router->generate('tfa_email_auth_form');

        return $redirectUrl;
    }

    public function getIdentifier(): ?string
    {
        return 'email';
    }

    public function getName(): ?string
    {
        return $this->translator->trans('email.provider.name', [], 'edgareztfa');
    }

    public function getDescription(): ?string
    {
        return $this->translator->trans('email.provider.description', [], 'edgareztfa');
    }
}
