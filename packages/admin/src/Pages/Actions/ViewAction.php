<?php

namespace Filament\Pages\Actions;

class ViewAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/view.single.label'));

        $this->color('secondary');

        $this->groupedIcon('heroicon-s-eye');
    }
}
