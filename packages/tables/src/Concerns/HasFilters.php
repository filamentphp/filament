<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    protected array $cachedTableFilters;

    public $tableFilters = [];

    public function cacheTableFilters(): void
    {
        $this->cachedTableFilters = collect($this->getTableFilters())
            ->mapWithKeys(function (Filter $filter): array {
                $filter->table($this->getCachedTable());

                return [$filter->getName() => $filter];
            })
            ->toArray();
    }

    public function getCachedTableFilters(): array
    {
        return $this->cachedTableFilters;
    }

    public function getTableFiltersForm(): Forms\ComponentContainer
    {
        return $this->tableFiltersForm;
    }

    public function isTableFilterable(): bool
    {
        return (bool) count($this->getCachedTableFilters());
    }

    public function updatedTableFilters(): void
    {
        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $data = $this->getTableFiltersForm()->getState();

        return $query->where(function (Builder $query) use ($data) {
            foreach ($this->getCachedTableFilters() as $filter) {
                $filter->apply(
                    $query,
                    $data[$filter->getName()] ?? [],
                );
            }
        });
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableFiltersFormSchema(): array
    {
        return array_map(
            fn (Filter $filter) => Forms\Components\Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filter->getName()),
            $this->getCachedTableFilters(),
        );
    }
}
