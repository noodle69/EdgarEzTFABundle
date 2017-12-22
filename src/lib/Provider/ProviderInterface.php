<?php

namespace Edgar\EzTFA\Provider;

use Edgar\EzTFA\Repository\EdgarEzTFARepository;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

interface ProviderInterface
{
    public function getIdentifier(): ?string;

    public function getName(): ?string;

    public function getDescription(): ?string;

    public function isAuthenticated(): bool;

    public function requestAuthCode(Request $request): ?string;

    public function register(EdgarEzTFARepository $tfaRepository, $userId, $provider): string;

    public function canBeMultiple(): bool;

    public function cancel(): void;

    public function purge(APIUser $user): void;
}
