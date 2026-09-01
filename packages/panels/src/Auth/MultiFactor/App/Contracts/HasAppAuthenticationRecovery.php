<?php

namespace Filament\Auth\MultiFactor\App\Contracts;

use SensitiveParameter;

interface HasAppAuthenticationRecovery
{
    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array;

    /**
     * @param  ?array<string>  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(#[SensitiveParameter] ?array $codes): void;
}
