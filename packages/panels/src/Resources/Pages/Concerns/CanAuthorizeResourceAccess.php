<?php

namespace Filament\Resources\Pages\Concerns;

trait CanAuthorizeResourceAccess
{
    public function mountCanAuthorizeResourceAccess(): void
    {
        static::authorizeResourceAccess();
    }

    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canAccess(), 403);

        if ($parentResource = static::getParentResource()) {
            abort_unless($parentResource::canAccess(), 403);
        }
    }
}
