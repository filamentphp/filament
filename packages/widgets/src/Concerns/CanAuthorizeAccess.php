<?php

namespace Filament\Widgets\Concerns;

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

        // Runs before the widget's `mount()` or `hydrate()`, including lazy mounts.
        abort_unless(static::canView(), 403);

        $this->hasAuthorizedAccess = true;
    }
}
