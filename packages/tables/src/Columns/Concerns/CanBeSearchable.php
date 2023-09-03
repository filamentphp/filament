<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait CanBeSearchable
{
    protected bool $isGloballySearchable = false;

    protected bool $isIndividuallySearchable = false;

    protected bool $isSearchable = false;

    /**
     * @var array<string> | null
     */
    protected ?array $searchColumns = null;

    protected ?Closure $searchQuery = null;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    /**
     * @param  bool | array<string> | string  $condition
     */
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

    public function forceSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    /**
     * @return array<string>
     */
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

    public function isSearchForcedCaseInsensitive(Builder $query): bool
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        return $this->evaluate($this->isSearchForcedCaseInsensitive) ?? match ($databaseConnection->getDriverName()) {
            'pgsql' => true,
            default => false,
        };
    }

    /**
     * @return array{0: string}
     */
    public function getDefaultSearchColumns(): array
    {
        return [(string) str($this->getName())->afterLast('.')];
    }
}
