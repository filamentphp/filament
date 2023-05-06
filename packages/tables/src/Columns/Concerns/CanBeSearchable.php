<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CanBeSearchable
{
    protected bool $isGloballySearchable = false;

    protected bool $isIndividuallySearchable = false;

    protected bool $isSearchable = false;

    protected ?array $searchColumns = null;

    protected ?Closure $searchQuery = null;

    public function searchable(
        bool | array | string $condition = true,
        ?Closure $query = null,
        bool $isIndividual = false,
        bool $isGlobal = true,
    ): static {
        if (is_bool($condition)) {
            $this->isSearchable = $condition;
            $this->searchColumns = null;
        } else {
            $this->isSearchable = true;
            $this->searchColumns = Arr::wrap($condition);
        }

        $this->isGloballySearchable = $isGlobal;
        $this->isIndividuallySearchable = $isIndividual;
        $this->searchQuery = $query;

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

    public function isGloballySearchable(): bool
    {
        return $this->isSearchable() && $this->isGloballySearchable;
    }

    public function isIndividuallySearchable(): bool
    {
        return $this->isSearchable() && $this->isIndividuallySearchable;
    }

    protected function getDefaultSearchColumns(): array
    {
        return [Str::of($this->getName())->afterLast('.')];
    }
}
