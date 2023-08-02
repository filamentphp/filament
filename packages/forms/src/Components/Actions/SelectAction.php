<?php

namespace Filament\Forms\Components\Actions;

use Filament\Actions\Concerns\HasSelect;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Concerns\HasState;
use Filament\Forms\Concerns\HasStateBindingModifiers;

class SelectAction extends Action
{
    use HasSelect;
    use HasState;
    use HasStateBindingModifiers;

    public function getParentComponent(): ComponentContainer
    {
        return $this->getComponent()->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->statePath = $this->getName();

        $this->view('filament-actions::select-action');
    }

    public function getExtraAttributes(): array
    {
        return array_merge(
            parent::getExtraAttributes(),
            [
                $this->applyStateBindingModifiers('wire:model') => $this->getStatePath(),
            ]
        );
    }
}
