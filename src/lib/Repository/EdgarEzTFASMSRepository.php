<?php

namespace Edgar\EzTFA\Repository;

use Edgar\EzTFABundle\Entity\EdgarEzTFASMS;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Doctrine\ORM\EntityRepository;

class EdgarEzTFASMSRepository extends EntityRepository
{
    public function savePhone(int $userId, string $phone)
    {
        $tfaSMS = new EdgarEzTFASMS();
        $tfaSMS->setUserId($userId);
        $tfaSMS->setPhone($phone);

        $this->getEntityManager()->persist($tfaSMS);
        $this->getEntityManager()->flush();
    }

    public function remove(EdgarEzTFASMS $tfaSMS)
    {
        $this->getEntityManager()->remove($tfaSMS);
        $this->getEntityManager()->flush();
    }

    public function purge(APIUser $user)
    {
        /** @var EdgarEzTFASMS[] $tfaSMSs */
        $tfaSMSs = $this->findByUserId($user->id);

        if ($tfaSMSs) {
            foreach ($tfaSMSs as $tfaSMS) {
                $this->getEntityManager()->remove($tfaSMS);
            }
            $this->getEntityManager()->flush();
        }
    }
}
