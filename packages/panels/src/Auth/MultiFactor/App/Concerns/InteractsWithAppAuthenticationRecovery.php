<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string> | null $app_authentication_recovery_codes
 *
 * @mixin Model
 */
trait InteractsWithAppAuthenticationRecovery /** @phpstan-ignore trait.unused */
{
    protected function initializeInteractsWithAppAuthenticationRecovery(): void
    {
        $this->mergeCasts([
            'app_authentication_recovery_codes' => 'encrypted:array',
        ]);

        $this->mergeHidden([
            'app_authentication_recovery_codes',
        ]);
    }

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  ?array<string>  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
