<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection;

trait InteractsWithRecords
{
    protected Collection | Closure | null $records = null;

    public function records(Collection | Closure | null $records): static
    {
        $this->records = $records;

        return $this;
    }

    public function getRecords(): ?Collection
    {
        return $this->evaluate(
            $this->records,
            exceptParameters: ['records'],
        );
    }
}
