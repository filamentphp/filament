<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Support\Attributes\ServerOnly;

trait CanAuthorizeAccess
{
    protected bool $hasAuthorizedAccess = false;

    #[ServerOnly]
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
