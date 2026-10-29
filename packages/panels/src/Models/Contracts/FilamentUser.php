<?php

namespace Filament\Models\Contracts;

use Filament\Panel;

interface FilamentUser
{
    // Security: You must implement this interface on your User model in
    // production. Without it, all authenticated users can access your
    // panel when `APP_ENV` is `local`. For multi-panel apps,
    // check `$panel->getId()` to restrict access per panel.

    public function canAccessPanel(Panel $panel): bool;
}
