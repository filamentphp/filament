<?php

namespace Filament\Widgets\Concerns;

trait CanAuthorizeAccess
{
    public function hydrateCanAuthorizeAccess(): void
    {
        abort_unless(static::canView(), 403);
    }
}
