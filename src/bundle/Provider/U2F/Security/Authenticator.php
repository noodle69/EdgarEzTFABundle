<?php

namespace Edgar\EzTFABundle\Provider\U2F\Security;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFABundle\Entity\EdgarEzTFAU2F;
use Edgar\EzTFA\Repository\EdgarEzTFAU2FRepository;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\RequestStack;
use u2flib_server\U2F;

class Authenticator
{
    /** @var U2F $u2f */
    protected $u2f;

    /** @var EdgarEzTFAU2FRepository $tfaU2FRepository */
    protected $tfaU2FRepository;

    /**
     * Authenticator constructor.
     *
     * @param RequestStack $requestStack
     * @param Registry $doctrineRegistry
     */
    public function __construct(
        RequestStack $requestStack,
        Registry $doctrineRegistry
    ) {
        $scheme = $requestStack->getCurrentRequest()->getScheme();
        $host = $requestStack->getCurrentRequest()->getHost();
        $port = $requestStack->getCurrentRequest()->getPort();
        $this->u2f = new U2F($scheme.'://'.$host.((80 !== $port && 443 !== $port)?':'.$port:''));

        $entityManager = $doctrineRegistry->getManager();
        $this->tfaU2FRepository = $entityManager->getRepository(EdgarEzTFAU2F::class);
    }

    public function generateRegistrationRequest(User $user)
    {
        $userU2Fs = $this->getUserKeys($user);

        return $this->u2f->getRegisterData($userU2Fs);
    }

    public function doRegistration($regRequest, $registration)
    {
        return $this->u2f->doRegister($regRequest, $registration);
    }

    /**
     * @param User $user
     * @param $request
     * @param $authData
     * @return \u2flib_server\Registration
     */
    public function checkRequest(User $user, $request, $authData)
    {
        $userU2Fs = $this->getUserKeys($user);

        return $this->u2f->doAuthenticate($request, $userU2Fs, $authData);
    }

    /**
     * @param User $user
     * @return array
     */
    public function generateRequest(User $user)
    {
        $userU2Fs = $this->getUserKeys($user);

        return $this->u2f->getAuthenticateData($userU2Fs);
    }

    /**
     * @param User $user
     * @return array
     */
    private function getUserKeys(User $user): array
    {
        $userKeys = [];

        $apiUser = $user->getAPIUser();
        /** @var EdgarEzTFAU2F[] $userU2Fs */
        $userU2Fs = $this->tfaU2FRepository->findByUserId($apiUser->id);

        foreach ($userU2Fs as $userU2F)
        {
            $userKey = (object)[
                'id' => $userU2F->getId(),
                'userId' => $userU2F->getUserId(),
                'keyName' => $userU2F->getKeyName(),
                'keyHandle' => $userU2F->getKeyHandle(),
                'publicKey' => $userU2F->getPublicKey(),
                'certificate' => $userU2F->getCertificate(),
                'counter' => $userU2F->getCounter()
            ];

            $userKeys[] = $userKey;
        }

        return $userKeys;
    }
}
