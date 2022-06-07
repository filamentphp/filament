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

        $this->label(fn (CreateAction $action): string => __('filament-support::actions/create.single.modal.heading', ['label' => $action->getModelLabel()]));
    }
}
