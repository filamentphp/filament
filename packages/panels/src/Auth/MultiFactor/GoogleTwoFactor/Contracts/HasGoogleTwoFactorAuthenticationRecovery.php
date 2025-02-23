<?php

namespace Filament\Auth\MultiFactor\GoogleTwoFactor\Contracts;

interface HasGoogleTwoFactorAuthenticationRecovery
{
    /**
     * @return array<string>
     */
    public function getGoogleTwoFactorAuthenticationRecoveryCodes(): array;

    /**
     * @param  array<string> | null  $codes
     */
    public function saveGoogleTwoFactorAuthenticationRecoveryCodes(?array $codes): void;
}
