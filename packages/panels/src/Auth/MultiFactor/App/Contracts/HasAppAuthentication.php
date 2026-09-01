<?php

namespace Filament\Auth\MultiFactor\App\Contracts;

use SensitiveParameter;

interface HasAppAuthentication
{
    public function getAppAuthenticationSecret(): ?string;

    public function saveAppAuthenticationSecret(#[SensitiveParameter] ?string $secret): void;

    public function getAppAuthenticationHolderName(): string;
}
