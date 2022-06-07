<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;

class CreateAction extends Action
{
    use CanCustomizeProcess;

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
