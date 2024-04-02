<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Contracts\View\View;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasEmptyState
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableEmptyState(): ?View
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<Action | ActionGroup>
     */
    protected function getTableEmptyStateActions(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableEmptyStateDescription(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableEmptyStateHeading(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableEmptyStateIcon(): ?string
    {
        return null;
    }
}
