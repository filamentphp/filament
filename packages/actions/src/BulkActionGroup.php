<?php

namespace Filament\Actions;

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\View\TablesIconAlias;

class BulkActionGroup extends ActionGroup
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-tables::table.actions.open_bulk_actions.label'));

        $this->icon(FilamentIcon::resolve(TablesIconAlias::ACTIONS_OPEN_BULK_ACTIONS) ?? Heroicon::EllipsisVertical);

        $this->defaultColor('gray');

        $this->button();

        $this->dropdownPlacement('bottom-start');

        $this->labeledFrom('sm');
    }

    /**
     * @return array<mixed>
     */
    public function getExtraDropdownAttributes(): array
    {
        return [
            'x-cloak' => true,
            'x-show' => 'getSelectedRecordsCount()',
            ...parent::getExtraAttributes(),
        ];
    }
}
