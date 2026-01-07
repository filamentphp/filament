<?php

namespace Filament\Auth\MultiFactor\Email\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bool $has_email_authentication
 *
 * @mixin Model
 */
trait InteractsWithEmailAuthentication /** @phpstan-ignore trait.unused */
{
    protected function initializeInteractsWithEmailAuthentication(): void
    {
        $this->mergeCasts([
            'has_email_authentication' => 'boolean',
        ]);
    }

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
