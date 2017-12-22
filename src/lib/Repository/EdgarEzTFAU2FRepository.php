<?php

namespace Edgar\EzTFA\Repository;

use Edgar\EzTFABundle\Entity\EdgarEzTFAU2F;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Doctrine\ORM\EntityRepository;
use u2flib_server\Registration;

class EdgarEzTFAU2FRepository extends EntityRepository
{
    public function saveKey(Registration $registrationData, int $userId, string $keyName)
    {
        $tfaU2F = new EdgarEzTFAU2F();
        $tfaU2F->setKeyName($keyName);
        $tfaU2F->setUserId($userId);
        $tfaU2F->setKeyHandle($registrationData->keyHandle);
        $tfaU2F->setPublicKey($registrationData->publicKey);
        $tfaU2F->setCertificate($registrationData->certificate);
        $tfaU2F->setCounter($registrationData->counter);

        $this->getEntityManager()->persist($tfaU2F);
        $this->getEntityManager()->flush();
    }

    public function remove(EdgarEzTFAU2F $tfaU2F)
    {
        $this->getEntityManager()->remove($tfaU2F);
        $this->getEntityManager()->flush();
    }

    public function purge(APIUser $user)
    {
        /** @var EdgarEzTFAU2F[] $tfaU2Fs */
        $tfaU2Fs = $this->findByUserId($user->id);

        if ($tfaU2Fs) {
            foreach ($tfaU2Fs as $tfaU2F) {
                $this->getEntityManager()->remove($tfaU2F);
            }
            $this->getEntityManager()->flush();
        }
    }
}
