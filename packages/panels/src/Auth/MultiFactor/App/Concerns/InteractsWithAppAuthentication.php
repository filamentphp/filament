<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

/**
 * @property ?string $app_authentication_secret
 *
 * @mixin Model
 */
trait InteractsWithAppAuthentication /** @phpstan-ignore trait.unused */
{
    protected function initializeInteractsWithAppAuthentication(): void
    {
        $this->mergeCasts([
            'app_authentication_secret' => 'encrypted',
        ]);

        if (version_compare(Application::VERSION, '12.25.0', '>=')) {
            $this->mergeHidden([
                'app_authentication_secret',
            ]);
        } else {
            $this->hidden = array_values(array_unique(array_merge($this->hidden, [
                'app_authentication_secret',
            ])));
        }
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
