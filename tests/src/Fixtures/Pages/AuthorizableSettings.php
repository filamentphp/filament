<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class AuthorizableSettings extends Page
{
    protected string $view = 'pages.settings';

    public static bool $canAccess = true;

    public ?string $name = null;

    public static function canAccess(): bool
    {
        return static::$canAccess;
    }
}
