<?php

namespace Filament\Pages\Actions;

class CreateAction extends Action
{
    public static function make(string $name = 'create'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/create.single.label'));
    }
}
