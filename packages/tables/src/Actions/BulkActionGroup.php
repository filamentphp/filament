<?php

namespace Filament\Tables\Actions;

use Filament\Support\Facades\FilamentIcon;

class BulkActionGroup extends ActionGroup
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-tables::table.actions.open_bulk_actions.label'));

        $this->icon(FilamentIcon::resolve('tables::actions.open-bulk-actions') ?? 'heroicon-m-ellipsis-vertical');

        $this->defaultColor('gray');

        $this->button();

        $this->dropdownPlacement('bottom-start');

        $this->labeledFrom('sm');
    }
}
