<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

trait HasContent
{
    protected function getTableContent(): ?View
    {
        return null;
    }

    protected function getTableContentGrid(): ?array
    {
        return null;
    }

    protected function getTableContentFooter(): ?View
    {
        return null;
    }
}
