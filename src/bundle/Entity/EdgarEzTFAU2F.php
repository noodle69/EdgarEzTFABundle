<?php

namespace Edgar\EzTFABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzTFAU2F
 *
 * @ORM\Entity(repositoryClass="Edgar\EzTFA\Repository\EdgarEzTFAU2FRepository")
 * @ORM\Table(name="edgar_ez_tfa_u2f")
 */
class EdgarEzTFAU2F
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
     * @ORM\Column(name="key_name", type="string", length=255, nullable=false)
     */
    private $keyName;

    /**
     * @var string
     *
     * @ORM\Column(name="key_handle", type="string", length=255, nullable=false)
     */
    private $keyHandle;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="string", length=255, nullable=false)
     */
    private $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="certificate", type="text", nullable=false)
     */
    private $certificate;

    /**
     * @var int
     *
     * @ORM\Column(name="counter", type="integer", nullable=false)
     */
    private $counter;


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
     * @return EdgarEzTFAU2F
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
     * Set keyName
     *
     * @param string $keyName
     *
     * @return EdgarEzTFAU2F
     */
    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * Get keyName
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * Set keyHandle
     *
     * @param string $keyHandle
     *
     * @return EdgarEzTFAU2F
     */
    public function setKeyHandle(string $keyHandle): self
    {
        $this->keyHandle = $keyHandle;

        return $this;
    }

    /**
     * Get keyHandle
     *
     * @return string
     */
    public function getKeyHandle(): string
    {
        return $this->keyHandle;
    }

    /**
     * Set publicKey
     *
     * @param string $publicKey
     *
     * @return EdgarEzTFAU2F
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get publicKey
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Set certificate
     *
     * @param string $certificate
     *
     * @return EdgarEzTFAU2F
     */
    public function setCertificate(string $certificate): self
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * Get certificate
     *
     * @return string
     */
    public function getCertificate(): string
    {
        return $this->certificate;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return EdgarEzTFAU2F
     */
    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }
}
