<?php

namespace Filament\Actions;

class SelectAction extends Action
{
    use Concerns\HasSelect;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-actions::select-action');
    }

    public function getExtraAttributes(): array
    {
        return array_merge(
            parent::getExtraAttributes(),
            [
                'wire:model.live' => $this->getName(),
            ]
        );
    }
}
