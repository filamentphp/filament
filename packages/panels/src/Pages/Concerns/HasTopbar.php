<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Components\Attributes\Unexposed;

trait HasTopbar
{
    protected bool $hasTopbar = true;

    #[Unexposed]
    public function hasTopbar(): bool
    {
        return $this->hasTopbar;
    }
}
