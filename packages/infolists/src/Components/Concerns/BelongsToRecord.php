<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait BelongsToRecord
{
    protected ?Model $record = null;

    public function record(?Model $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getRecord(): ?Model
    {
        if ($this->record instanceof Model) {
            return $this->record;
        }

        return $this->getContainer()->getRecord();
    }
}
