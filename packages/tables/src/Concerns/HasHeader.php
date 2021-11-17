<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

trait HasHeader
{
    protected function getTableDescription(): ?string
    {
        return null;
    }

    protected function getTableHeader(): ?View
    {
        return null;
    }

    protected function getTableHeaderActions(): array
    {
        return [];
    }

    protected function getTableHeading(): ?string
    {
        return null;
    }
}
