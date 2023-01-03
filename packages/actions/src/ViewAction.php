<?php

namespace Filament\Actions;

class ViewAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::view.single.label'));

        $this->color('gray');

        $this->groupedIcon('heroicon-m-eye');

        $this->disabledForm();
    }
}
