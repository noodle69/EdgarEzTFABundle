<?php

namespace Edgar\EzTFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzTFASMS
 *
 * @ORM\Entity(repositoryClass="Edgar\EzTFA\Repository\EdgarEzTFASMSRepository")
 * @ORM\Table(name="edgar_ez_tfa_sms")
 */
class EdgarEzTFASMS
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;


    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return EdgarEzTFASMS
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return EdgarEzTFASMS
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }
}
