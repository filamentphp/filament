<?php

namespace Filament\Tests\Fixtures\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroupEnum: string implements HasLabel
{
    case Users = 'users';
    case Settings = 'settings';

    public function getLabel(): string
    {
        return match ($this) {
            self::Users => 'User Management',
            self::Settings => 'System Settings',
        };
    }
}
