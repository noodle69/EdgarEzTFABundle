<?php

namespace Edgar\EzTFA\Repository;

use Edgar\EzTFABundle\Entity\EdgarEzTFA;
use Doctrine\ORM\EntityRepository;

class EdgarEzTFARepository extends EntityRepository
{
    public function setProvider(int $userId, string $provider)
    {
        $tfa = new EdgarEzTFA();
        $tfa->setUserId($userId);
        $tfa->setProvider($provider);

        $this->getEntityManager()->persist($tfa);
        $this->getEntityManager()->flush();
    }

    public function remove(EdgarEzTFA $tfaProvider)
    {
        $this->getEntityManager()->remove($tfaProvider);
        $this->getEntityManager()->flush();
    }
}
