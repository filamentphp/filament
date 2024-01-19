<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

trait CanAccessSelectedRecords
{
    protected bool | Closure $canAccessSelectedRecords = false;

    public function accessSelectedRecords(bool | Closure $condition = true): static
    {
        $this->canAccessSelectedRecords = $condition;

        return $this;
    }

    public function canAccessSelectedRecords(): bool
    {
        return (bool) $this->evaluate($this->canAccessSelectedRecords);
    }

    public function getSelectedRecords(): EloquentCollection | Collection
    {
        if (! $this->canAccessSelectedRecords()) {
            throw new Exception("The table action [{$this->getName()}] is attempting to access the selected records from the table, but it is not using [accessSelectedRecords()], so they are not available.");
        }

        return $this->getLivewire()->getSelectedTableRecords($this->shouldFetchSelectedRecords());
    }
}
