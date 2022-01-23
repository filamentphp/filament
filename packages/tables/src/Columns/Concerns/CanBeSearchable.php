<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Support\Str;

trait CanBeSearchable
{
    protected bool $isSearchable = false;

    protected ?array $searchColumns = null;

    public function searchable(bool | array $condition = true): static
    {
        if (is_array($condition)) {
            $this->isSearchable = true;
            $this->searchColumns = $condition;
        } else {
            $this->isSearchable = $condition;
            $this->searchColumns = null;
        }

        return $this;
    }

    public function getSearchColumns(): array
    {
        return $this->searchColumns ?? $this->getDefaultSearchColumns();
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    protected function getDefaultSearchColumns(): array
    {
        return [Str::of($this->getName())->afterLast('.')];
    }
}
