<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

/**
 * @property ?string $app_authentication_secret
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ImplementsAppAuthentication
{
    protected function initializeImplementsAppAuthentication(): void
    {
        $this->mergeCasts([
            'app_authentication_secret' => 'encrypted',
        ]);

        $this->mergeHidden([
            'app_authentication_secret',
        ]);
    }

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }
}
