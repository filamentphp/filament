<?php

namespace Filament\Auth\MultiFactor\App\Contracts;

interface HasAppAuthenticationRecovery
{
    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array;

    /**
     * @param  array<string> | null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void;
}
