<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

/**
 * @property array<string> | null $app_authentication_recovery_codes
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ImplementsAppAuthenticationRecovery
{
    protected function initializeImplementsAppAuthenticationRecovery(): void
    {
        $this->mergeCasts([
            'app_authentication_recovery_codes' => 'encrypted:array',
        ]);

        $this->mergeHidden([
            'app_authentication_recovery_codes',
        ]);
    }

    /**
     * @return array<string> | null
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  array<string> | null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
