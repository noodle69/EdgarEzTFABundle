<?php

namespace Edgar\EzTFABundle\Controller;

use Edgar\EzTFABundle\Provider\Email\Form\Data\EmailData;
use Edgar\EzTFABundle\Provider\Email\Form\Factory\FormFactory;
use Edgar\EzTFABundle\Provider\Email\Form\SubmitHandler;
use Edgar\EzTFABundle\Provider\Email\Form\Type\AuthType;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\TranslatorInterface;

class EmailAuthController extends Controller
{
    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var ConfigResolverInterface $configResolver */
    protected $configResolver;

    /** @var \Swift_Mailer $mailer */
    protected $mailer;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var array $providers */
    protected $providers;

    /** @var Session $session */
    protected $session;

    /** @var FormFactory  */
    protected $formFactory;

    /** @var SubmitHandler  */
    protected $submitHandler;

    public function __construct(
        TokenStorage $tokenStorage,
        ConfigResolverInterface $configResolver,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        array $providers,
        Session $session,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->configResolver = $configResolver;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->providers = $providers;

        $this->session = $session;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * Send TFA code by email
     *
     * @param string $code
     * @param string $emailFrom
     * @param string $emailTo
     */
    protected function sendCode(int $code, string $emailFrom, string $emailTo)
    {
        $message = \Swift_Message::newInstance();

        $message->setSubject($this->translator->trans('Two Factor Authentication code', [], 'edgareztfa'))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->renderView(
                    'EdgarEzTFABundle:profile:tfa/email/mail.txt.twig',
                    [
                        'code' => $code,
                    ]
                ), 'text/html'
            );

        $this->mailer->send($message);
    }

    public function authAction(Request $request): Response
    {
        $actionUrl = $this->generateUrl('tfa_email_auth_form');

        $authType = $this->formFactory->configure(
            new EmailData()
        );
        $authType->handleRequest($request);

        if ($authType->isSubmitted()) {
            $result = $this->submitHandler->handle($authType, function (EmailData $data) use ($authType) {
                $this->session->set('tfa_authenticated', true);
                return new RedirectResponse($this->session->get('tfa_redirecturi'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $code = $this->session->get('tfa_authcode', false);

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();
        $mailTo = $apiUser->email;
        $mailFrom = $this->providers['email']['from'];

        $codeSended = $this->session->get('tfa_codesended', false);
        if (!$codeSended) {
            $this->sendCode($code, $mailFrom, $mailTo);
            $this->session->set('tfa_codesended', true);
        }

        return $this->render('EdgarEzTFABundle:profile:tfa/email/auth.html.twig', [
            'layout' => $this->configResolver->getParameter('pagelayout'),
            'form' => $authType->createView(),
            'actionUrl' => $actionUrl,
            'code' => $code,
            'error' => $authType->getErrors(),
        ]);
    }
}
