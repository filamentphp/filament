<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Auth\Pages\EditProfile;

class EditProfileWithCustomRedirect extends EditProfile
{
    protected function getRedirectUrl(): ?string
    {
        return '/custom-redirect';
    }
}
