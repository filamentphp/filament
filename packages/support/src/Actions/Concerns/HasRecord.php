<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRecord
{
    protected Model | Closure | null $record = null;

    public function record(Model | Closure | null $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getRecord(): ?Model
    {
        return $this->evaluate($this->record);
    }
}
