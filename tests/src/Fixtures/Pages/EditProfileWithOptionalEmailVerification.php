<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Panel;

class EditProfileWithOptionalEmailVerification extends EditProfile
{
    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return false;
    }
}
