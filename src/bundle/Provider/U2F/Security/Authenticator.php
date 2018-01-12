<?php

namespace Edgar\EzTFABundle\Provider\U2F\Security;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Edgar\EzTFABundle\Entity\EdgarEzTFAU2F;
use Edgar\EzTFA\Repository\EdgarEzTFAU2FRepository;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\HttpFoundation\RequestStack;
use u2flib_server\Error;
use u2flib_server\Registration;
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

    /**
     * @param User $user
     * @return array
     * @throws Error
     */
    public function generateRegistrationRequest(User $user): array
    {
        $userU2Fs = $this->getUserKeys($user);

        try {
            $registerData = $this->u2f->getRegisterData($userU2Fs);
            return $registerData;
        } catch (Error $e) {
            throw $e;
        }
    }

    /**
     * @param $regRequest
     * @param $registration
     * @return null|Registration
     * @throws Error
     */
    public function doRegistration($regRequest, $registration): Registration
    {
        try {
            $registration = $this->u2f->doRegister($regRequest, $registration);
            return $registration;
        } catch (Error $e) {
            throw $e;
        }
    }

    /**
     * @param User $user
     * @param array $request
     * @param $authData
     * @return null|Registration
     * @throws Error
     */
    public function checkRequest(User $user, array $request, $authData)
    {
        $userU2Fs = $this->getUserKeys($user);

        try {
            $registration = $this->u2f->doAuthenticate($request, $userU2Fs, $authData);
            return $registration;
        } catch (Error $e) {
            throw $e;
        }
    }

    /**
     * @param User $user
     * @return array
     * @throws Error
     */
    public function generateRequest(User $user): array
    {
        $userU2Fs = $this->getUserKeys($user);

        try {
            $authenticateData = $this->u2f->getAuthenticateData($userU2Fs);
            return $authenticateData;
        } catch (Error $e) {
            throw $e;
        }
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
