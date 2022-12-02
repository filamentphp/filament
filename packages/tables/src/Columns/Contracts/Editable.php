<?php

namespace Filament\Tables\Columns\Contracts;

use Livewire\Component;

interface Editable
{
    public function validate($input): void;

    public function saveState(Component $table, mixed $state): mixed;
}
