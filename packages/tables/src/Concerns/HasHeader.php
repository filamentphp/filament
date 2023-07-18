<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasHeader
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableDescription(): string | Htmlable | null
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableHeader(): View | Htmlable | null
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<Action | BulkAction | ActionGroup>
     */
    protected function getTableHeaderActions(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableHeading(): string | Htmlable | null
    {
        return null;
    }
}
