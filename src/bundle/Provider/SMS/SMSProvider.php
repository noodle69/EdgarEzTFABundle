<?php

namespace Edgar\EzTFABundle\Provider\SMS;

use Edgar\EzTFA\Provider\AbstractProvider;
use Edgar\EzTFA\Provider\ProviderInterface;
use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Edgar\EzTFA\Repository\EdgarEzTFASMSRepository;
use Edgar\EzTFABundle\Entity\EdgarEzTFASMS;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\RouterInterface;

class SMSProvider extends AbstractProvider implements ProviderInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var EdgarEzTFASMSRepository $tfaSMSRepository */
    protected $tfaSMSRepository;

    /**
     * SMSProvider constructor.
     *
     * @param Router $router
     * @param Session $session
     * @param Translator $translator
     */
    public function __construct(
        RouterInterface $router,
        Session $session,
        Translator $translator,
        Registry $doctrineRegistry
    ) {
        parent::__construct($session, $translator);
        $this->router = $router;

        $entityManager = $doctrineRegistry->getManager();
        /** @var EdgarEzTFASMSRepository tfaSMSRepository */
        $this->tfaSMSRepository = $entityManager->getRepository(EdgarEzTFASMS::class);
    }

    /**
     * Return url to request auth code
     *
     * @param Request $request
     * @return string
     */
    public function requestAuthCode(Request $request)
    {
        $authCode = random_int(100000, 999999);
        $this->session->set('tfa_authcode', $authCode);
        $this->session->set('tfa_redirecturi', $request->getUri());

        $redirectUrl =  $this->router->generate('tfa_sms_auth_form');

        return $redirectUrl;
    }

    public function register(
        EdgarEzTFARepository $tfaRepository,
        $userId, $provider
    ) {
        return $this->router->generate('tfa_sms_register_form');
    }

    public function getIdentifier()
    {
        return 'sms';
    }

    public function getName()
    {
        return $this->translator->trans('sms.provider.name', [], 'edgareztfa');
    }

    public function getDescription()
    {
        return $this->translator->trans('sms.provider.description', [], 'edgareztfa');
    }

    public function purge(APIUser $user)
    {
        $this->tfaSMSRepository->purge($user);
    }
}
