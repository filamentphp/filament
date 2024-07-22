<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRecord
{
    protected ?Model $record = null;

    public function record(Model $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getRecord(): ?Model
    {
        return $this->record ?? $this->getLayout()?->getRecord();
    }
}
