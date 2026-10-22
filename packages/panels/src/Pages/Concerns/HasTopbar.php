<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Attributes\ServerOnly;

trait HasTopbar
{
    protected bool $hasTopbar = true;

    #[ServerOnly]
    public function hasTopbar(): bool
    {
        return $this->hasTopbar;
    }
}
