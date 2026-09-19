<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Pages\Page;

class TestPage extends Page
{
    use InteractsWithTestInertia;

    protected static ?string $slug = 'page';

    public static function canAccess(): bool
    {
        return RequestState::$allowed;
    }
}
