<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

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

        if (version_compare(Application::VERSION, '12.25.0', '>=')) {
            $this->mergeHidden([
                'app_authentication_recovery_codes',
            ]);
        } else {
            $this->hidden = array_values(array_unique(array_merge($this->hidden, [
                'app_authentication_recovery_codes',
            ])));
        }
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
