<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Actions\Concerns;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class SelectAction extends Action
{
    use Concerns\HasSelect;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-actions::select-action');
    }
}
