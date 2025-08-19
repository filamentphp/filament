<?php

namespace Filament\Auth\MultiFactor\App\Concerns;

trait InteractsWithEmailAuthentication
{
    public function hasEmailAuthentication(): bool
    {
        return (bool) $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->has_email_authentication = $condition;
        $this->save();
    }
}
