<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;

class ProfileEmailChangeMailUser extends User implements HasLocalePreference
{
    protected $table = 'users';

    /**
     * @return array<string, string>
     */
    public function routeNotificationForMail(): array
    {
        return [$this->email => $this->name];
    }

    public function preferredLocale(): string
    {
        return 'fr';
    }
}
