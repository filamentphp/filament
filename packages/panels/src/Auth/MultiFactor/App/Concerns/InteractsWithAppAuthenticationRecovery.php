<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

trait InteractsWithAppAuthenticationRecovery
{
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
