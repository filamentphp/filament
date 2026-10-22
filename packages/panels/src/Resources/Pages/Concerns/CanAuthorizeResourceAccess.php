<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Support\Attributes\ServerOnly;

trait CanAuthorizeResourceAccess
{
    #[ServerOnly]
    public function mountCanAuthorizeResourceAccess(): void
    {
        static::authorizeResourceAccess();
    }

    #[ServerOnly]
    public function hydrateCanAuthorizeResourceAccess(): void
    {
        static::authorizeResourceAccess();
    }

    #[ServerOnly]
    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canAccess(), 403);
    }
}
