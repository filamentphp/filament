<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Layout;
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
        $this->cachedTableFilters = [];

        foreach ($this->getTableFilters() as $filter) {
            $filter->table($this->getCachedTable());

            $this->cachedTableFilters[$filter->getName()] = $filter;
        }
    }

    public function getCachedTableFilters(): array
    {
        return array_filter(
            $this->cachedTableFilters,
            fn (BaseFilter $filter): bool => ! $filter->isHidden(),
        );
    }

    public function getCachedTableFilter(string $name): ?BaseFilter
    {
        return $this->getCachedTableFilters()[$name] ?? null;
    }

    public function getTableFiltersForm(): Forms\ComponentContainer
    {
        if ((! $this->isCachingForms) && $this->hasCachedForm('tableFiltersForm')) {
            return $this->getCachedForm('tableFiltersForm');
        }

        return $this->makeForm()
            ->schema($this->getTableFiltersFormSchema())
            ->columns($this->getTableFiltersFormColumns())
            ->statePath('tableFilters')
            ->reactive();
    }

    public function isTableFilterable(): bool
    {
        return (bool) count($this->getCachedTableFilters());
    }

    public function updatedTableFilters(): void
    {
        if ($this->shouldPersistTableFiltersInSession()) {
            session()->put(
                $this->getTableFiltersSessionKey(),
                $this->tableFilters,
            );
        }

        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    public function removeTableFilter(string $filter, ?string $field = null): void
    {
        $filterGroup = $this->getTableFiltersForm()->getComponents()[$filter];
        $fields = $filterGroup?->getChildComponentContainer()->getFlatFields() ?? [];

        if (filled($field) && array_key_exists($field, $fields)) {
            $fields = [$fields[$field]];
        }

        foreach ($fields as $field) {
            $state = $field->getState();

            $field->state(match (true) {
                is_array($state) => [],
                $state === true => false,
                default => null,
            });
        }

        $this->updatedTableFilters();
    }

    public function removeTableFilters(): void
    {
        foreach ($this->getTableFiltersForm()->getFlatFields(withAbsolutePathKeys: true) as $field) {
            $state = $field->getState();

            $field->state(match (true) {
                is_array($state) => [],
                $state === true => false,
                default => null,
            });
        }

        $this->updatedTableFilters();
    }

    public function resetTableFiltersForm(): void
    {
        $this->getTableFiltersForm()->fill();

        $this->updatedTableFilters();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $data = $this->getTableFiltersForm()->getRawState();

        foreach ($this->getCachedTableFilters() as $filter) {
            $filter->applyToBaseQuery(
                $query,
                $data[$filter->getName()] ?? [],
            );
        }

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
        return match ($this->getTableFiltersLayout()) {
            Layout::AboveContent, Layout::BelowContent => [
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ],
            default => 1,
        };
    }

    protected function getTableFiltersFormSchema(): array
    {
        $schema = [];

        foreach ($this->getCachedTableFilters() as $filter) {
            $schema[$filter->getName()] = Forms\Components\Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filter->getName());
        }

        return $schema;
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

    protected function getTableFiltersLayout(): ?string
    {
        return null;
    }

    public function getTableFiltersSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_filters";
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return false;
    }
}
