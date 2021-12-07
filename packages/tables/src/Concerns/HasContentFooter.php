<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

trait HasContentFooter
{
    protected function getTableContentFooter(): ?View
    {
        return null;
    }
}
