<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Enums\Width;

trait HasMaxWidth
{
    protected Width | string | null $maxWidth = null;

    #[Unexposed]
    public function getMaxWidth(): Width | string | null
    {
        return $this->maxWidth;
    }
}
