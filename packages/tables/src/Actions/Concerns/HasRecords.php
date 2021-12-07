<?php

namespace Filament\Tables\Actions\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait HasRecords
{
    protected ?Collection $records = null;

    public function records(Collection $records): static
    {
        $this->records = $records;

        return $this;
    }

    public function getRecords(): ?Collection
    {
        return $this->records;
    }
}
