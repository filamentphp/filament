<?php

namespace Filament\Pages\Concerns;

trait CanAuthorizeAccess
{
    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
