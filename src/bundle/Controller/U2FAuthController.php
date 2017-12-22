<?php

namespace Edgar\EzTFABundle\Controller;

use Edgar\EzTFABundle\Provider\U2F\Form\Type\AuthType;
use Edgar\EzTFABundle\Provider\U2F\Security\Authenticator;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class U2FAuthController extends Controller
{
    /** @var ConfigResolverInterface $configResolver */
    protected $configResolver;

    /** @var Authenticator $authenticator */
    protected $authenticator;

    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var Session $session */
    protected $session;

    /**
     * AuthController constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param Authenticator $authenticator
     * @param TokenStorage $tokenStorage
     * @param Session $session
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        Authenticator $authenticator,
        TokenStorage $tokenStorage,
        Session $session
    ) {
        $this->configResolver = $configResolver;
        $this->authenticator = $authenticator;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * Ask user for an Key and authenticate
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function authAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $actionUrl = $this->generateUrl('tfa_u2f_auth_form');

        $form = $this->createForm(AuthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->session->set('tfa_authenticated', true);
                return new RedirectResponse($this->session->get('tfa_redirecturi'));
            } else {
                $this->session->getFlashBag()->set('two_factor', 'u2f.code_invalid');
            }
        }

        $authenticationData = json_encode($this->authenticator->generateRequest($user), JSON_UNESCAPED_SLASHES);
        $this->session->set('u2f_authentication', $authenticationData);

        return $this->render('EdgarEzTFABundle:profile:tfa/u2f/auth.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $actionUrl,
            'authenticationData' => $authenticationData
        ]);
    }
}
