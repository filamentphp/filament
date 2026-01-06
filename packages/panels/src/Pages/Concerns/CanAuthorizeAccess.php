<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use LogicException;

trait CanAuthorizeAccess
{
    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public static function canAccess(): bool
    {
        return Filament::isAuthorizationStrict()
        ? throw new LogicException(sprintf('Strict authorization mode is enabled, but [canAccess()] method in [%s] class is not defined.', static::class))
        : true;
    }
}
