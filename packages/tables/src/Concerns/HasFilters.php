<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property ComponentContainer $tableFiltersForm
 */
trait HasFilters
{
    protected array $cachedTableFilters;

    public $tableFilters = null;

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
        return collect($this->cachedTableFilters)
            ->filter(fn (Filter $filter): bool => ! $filter->isHidden())
            ->toArray();
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

    public function resetTableFiltersForm(): void
    {
        $this->getTableFiltersForm()->fill();
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

    protected function getTableFiltersFormColumns(): int | array
    {
        return 1;
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

    protected function getTableFiltersFormWidth(): ?string
    {
        return match ($this->getTableFiltersFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
    }
}
