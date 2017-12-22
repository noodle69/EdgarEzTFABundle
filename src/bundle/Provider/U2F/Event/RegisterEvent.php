<?php

namespace Edgar\EzTFABundle\Provider\U2F\Event;

use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use u2flib_server\Registration;

class RegisterEvent extends Event
{
    const TFA_U2F_REGISTER = 'edgarez_tfa_u2f.register';

    protected $registration;

    /**
     * @var User
     **/
    protected $user;

    /**
     * @var string
     **/
    protected $keyName;

    /**
     * @var Response
     **/
    protected $response;

    /**
     * RegisterEvent constructor.
     *
     * @param $registration
     * @param $user
     * @param $name
     */
    public function __construct(Registration $registration, User $user, string $name)
    {
        $this->registration = $registration;
        $this->user = $user;
        $this->keyName = $name;
    }

    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    /**
     * getUser
     *
     * @return mixed
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * setUser
     *
     * @param mixed $user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * getResponse
     *
     * @return mixed
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * setResponse
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * getKeyName
     *
     * @return mixed
     */
    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * setKeyName
     *
     * @param mixed $keyName
     *
     * @return $this
     */
    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }
}
