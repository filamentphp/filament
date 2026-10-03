<?php

namespace Filament\Tables\Columns\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Editable
{
    public function validate(mixed $input): void;

    public function updateState(mixed $state, ?Model $relatedRecord = null): mixed;
}
