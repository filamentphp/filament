<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Pages\SimplePage;

class TestSimplePage extends SimplePage
{
    use InteractsWithTestInertia;

    protected function authorizeInertiaAccess(): void
    {
        abort_unless(RequestState::$allowed, 403);
    }
}
