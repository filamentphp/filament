<?php

namespace Filament\Resources\RelationManagers\Concerns;

trait CanAuthorizeAccess
{
    public function hydrateCanAuthorizeAccess(): void
    {
        abort_unless(static::canViewForRecord($this->ownerRecord, $this->pageClass ?? static::class), 403);
    }
}
