<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Support\Attributes\Unexposed;

trait CanAuthorizeResourceAccess
{
    #[Unexposed]
    public function mountCanAuthorizeResourceAccess(): void
    {
        static::authorizeResourceAccess();
    }

    #[Unexposed]
    public function hydrateCanAuthorizeResourceAccess(): void
    {
        static::authorizeResourceAccess();
    }

    #[Unexposed]
    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canAccess(), 403);
    }
}
