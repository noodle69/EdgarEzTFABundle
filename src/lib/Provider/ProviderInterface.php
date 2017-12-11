<?php

namespace Edgar\EzTFA\Provider;

use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

interface ProviderInterface
{
    public function getIdentifier();

    public function getName();

    public function getDescription();

    public function isAuthenticated();

    public function requestAuthCode(Request $request);

    public function register(EdgarEzTFARepository $tfaRepository, $userId, $provider);

    public function canBeMultiple();

    public function cancel();

    public function purge(APIUser $user);
}
