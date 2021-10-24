<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    use Forms\Concerns\InteractsWithForms;

    protected array $cachedTableFilters;

    public $tableFilters = [];

    public function cacheTableFilters(): void
    {
        $this->cachedTableFilters = collect($this->getTableFilters())
            ->mapWithKeys(function (Filter $filter): array {
                $filter->table($this->getTable());

                return [$filter->getName() => $filter];
            })
            ->toArray();
    }

    public function getCachedTableFilters(): array
    {
        return $this->cachedTableFilters;
    }

    public function getTableFilters(): array
    {
        return [];
    }

    public function isTableFilterable(): bool
    {
        return (bool) count($this->getCachedTableFilters());
    }

    public function updatedTableFilters(): void
    {
        $this->selected = [];

        $this->resetPage();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $data = $this->filtersForm->getState();

        return $query->where(function (Builder $query) use ($data) {
            foreach ($this->getCachedTableFilters() as $filter) {
                $filter->apply(
                    $query,
                    $data[$filter->getName()] ?? [],
                );
            }
        });
    }

    protected function getForms(): array
    {
        return [
            'filtersForm' => $this->makeForm()
                ->schema($this->getTableFiltersFormSchema())
                ->statePath('tableFilters')
                ->reactive(),
        ];
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
