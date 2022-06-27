<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;

class EditAction extends Action
{
    use CanCustomizeProcess;

    public static function make(string $name = 'edit'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/edit.single.label'));

        $this->groupedIcon('heroicon-s-pencil');
    }
}
