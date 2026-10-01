<?php

namespace Filament\Resources\RelationManagers\Concerns;

trait CanAuthorizeAccess
{
    protected bool $hasAuthorizedAccess = false;

    public function bootCanAuthorizeAccess(): void
    {
        if ($this->hasAuthorizedAccess) {
            return;
        }

        // Runs before the relation manager's `mount()` or `hydrate()`, including lazy mounts.
        abort_unless(static::canViewForRecord($this->ownerRecord, $this->pageClass ?? static::class), 403);

        $this->hasAuthorizedAccess = true;
    }
}
