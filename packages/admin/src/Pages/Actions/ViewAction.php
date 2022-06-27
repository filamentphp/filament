<?php

namespace Filament\Pages\Actions;

class ViewAction extends Action
{
    public static function make(string $name = 'view'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/view.single.label'));

        $this->color('secondary');

        $this->groupedIcon('heroicon-s-eye');
    }
}
