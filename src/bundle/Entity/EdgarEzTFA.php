<?php

namespace Edgar\EzTFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzTFA
 *
 * @ORM\Entity(repositoryClass="Edgar\EzTFA\Repository\EdgarEzTFARepository")
 * @ORM\Table(name="edgar_ez_tfa")
 */
class EdgarEzTFA
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
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
     * @ORM\Column(name="provider", type="string", length=255, nullable=false)
     */
    private $provider;


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
     * @return EdgarEzTFA
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
     * Set provider
     *
     * @param string $provider
     *
     * @return EdgarEzTFA
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
