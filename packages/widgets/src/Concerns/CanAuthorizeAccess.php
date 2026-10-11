<?php

namespace Filament\Widgets\Concerns;

trait CanAuthorizeAccess
{
    protected bool $hasAuthorizedAccess = false;

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
