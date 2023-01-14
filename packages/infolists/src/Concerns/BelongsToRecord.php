<?php

namespace Filament\Infolists\Concerns;

use Illuminate\Database\Eloquent\Model;

trait BelongsToRecord
{
    public ?Model $record = null;

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

        return $this->getParentComponent()?->getRecord();
    }
}
