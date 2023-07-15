<?php

namespace Filament\Tables\Actions;

class BulkActionGroup extends ActionGroup
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-tables::table.actions.open_bulk_actions.label'));

        $this->icon('heroicon-m-ellipsis-vertical');

        $this->color('gray');

        $this->button();

        $this->dropdownPlacement('bottom-start');

        $this->labeledFrom('sm');
    }
}
