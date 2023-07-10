<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns;

class SelectAction extends Action
{
    use Concerns\HasSelect;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-actions::select-action');
    }
}
