<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

trait HasEmptyState
{
    public function getTableEmptyStateDescription(): ?string
    {
        return null;
    }

    public function getTableEmptyStateHeading(): string
    {
        return __('tables::table.empty.heading');
    }

    public function getTableEmptyStateIcon(): string
    {
        return 'heroicon-o-x';
    }

    public function getTableEmptyStateView(): ?View
    {
        return null;
    }
}
