<?php

namespace Filament\Auth\MultiFactor\Email\Concerns;

/**
 * @property bool $has_email_authentication
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ImplementsEmailAuthentication
{
    protected function initializeImplementsEmailAuthentication(): void
    {
        $this->mergeCasts([
            'has_email_authentication' => 'boolean',
        ]);
    }

    public function hasEmailAuthentication(): bool
    {
        return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->has_email_authentication = $condition;
        $this->save();
    }
}
