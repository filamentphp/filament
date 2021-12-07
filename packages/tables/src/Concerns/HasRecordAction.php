<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRecordAction
{
    protected function getRecordAction(Model $record): ?string
    {
        return null;
    }
}
