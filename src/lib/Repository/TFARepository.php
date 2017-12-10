<?php

namespace Edgar\EzTFA\Repository;

use Edgar\EzTFABundle\Entity\TFA;

class TFARepository extends \Doctrine\ORM\EntityRepository
{
    public function setProvider($userId, $provider)
    {
        $tfa = new TFA();
        $tfa->setUserId($userId);
        $tfa->setProvider($provider);

        $this->getEntityManager()->persist($tfa);
        $this->getEntityManager()->flush();
    }

    public function remove(TFA $tfaProvider)
    {
        $this->getEntityManager()->remove($tfaProvider);
        $this->getEntityManager()->flush();
    }
}
