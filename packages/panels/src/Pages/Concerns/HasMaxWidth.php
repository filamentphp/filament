<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Attributes\ServerOnly;
use Filament\Support\Enums\Width;

trait HasMaxWidth
{
    protected Width | string | null $maxWidth = null;

    #[ServerOnly]
    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}
