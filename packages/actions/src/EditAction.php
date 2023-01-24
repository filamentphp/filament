<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;

class EditAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::edit.single.label'));

        $this->groupedIcon('heroicon-m-pencil-square');
    }
}
