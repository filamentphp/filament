<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

trait HasEmptyState
{
    protected function getTableEmptyStateDescription(): ?string
    {
        return null;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return null;
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return null;
    }

    protected function getTableEmptyStateView(): ?View
    {
        return null;
    }
}
