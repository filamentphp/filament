<?php

namespace Filament\Auth\MultiFactor\EmailCode\Contracts;

interface HasEmailCodeAuthentication
{
    public function hasEmailCodeAuthentication(): bool;

    public function getEmailCodeAuthenticationSecret(): string;

    public function saveEmailCodeAuthenticationSecret(?string $secret): void;
}
