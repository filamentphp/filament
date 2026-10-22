<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Attributes\ServerOnly;

trait CanAuthorizeAccess
{
    #[ServerOnly]
    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    #[ServerOnly]
    public function hydrateCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    #[ServerOnly]
    public static function canAccess(): bool
    {
        // Security: Custom pages default to allowing access for all
        // authenticated panel users. Override this method to restrict
        // access based on roles, permissions, or other logic.

        return true;
    }
}
