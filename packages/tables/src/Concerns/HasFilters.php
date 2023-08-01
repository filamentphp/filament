<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Form $tableFiltersForm
 */
trait HasFilters
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $tableFilters = null;

    public function getTableFiltersForm(): Form
    {
        if ((! $this->isCachingForms) && $this->hasCachedForm('tableFiltersForm')) {
            return $this->getForm('tableFiltersForm');
        }

        return $this->makeForm()
            ->schema($this->getTableFiltersFormSchema())
            ->columns($this->getTable()->getFiltersFormColumns())
            ->model($this->getTable()->getModel())
            ->statePath('tableFilters')
            ->live();
    }

    public function updatedTableFilters(): void
    {
        if ($this->getTable()->persistsFiltersInSession()) {
            session()->put(
                $this->getTableFiltersSessionKey(),
                $this->tableFilters,
            );
        }

        if ($this->getTable()->shouldDeselectAllRecordsWhenFiltered()) {
            $this->deselectAllTableRecords();
        }

        $this->resetPage();
    }

    public function removeTableFilter(string $filterName, ?string $field = null, bool $shouldTriggerUpdatedFiltersHook = true): void
    {
        $filter = $this->getTable()->getFilter($filterName);
        $filterResetState = $filter->getResetState();

        $filterFormGroup = $this->getTableFiltersForm()->getComponents()[$filterName] ?? null;
        $filterFields = $filterFormGroup?->getChildComponentContainer()->getFlatFields();

        if (filled($field) && array_key_exists($field, $filterFields)) {
            $filterFields = [$field => $filterFields[$field]];
        }

        foreach ($filterFields as $fieldName => $field) {
            $state = $field->getState();

            $field->state($filterResetState[$fieldName] ?? match (true) {
                is_array($state) => [],
                is_bool($state) => false,
                default => null,
            });
        }

        if (! $shouldTriggerUpdatedFiltersHook) {
            return;
        }

        $this->updatedTableFilters();
    }

    public function removeTableFilters(): void
    {
        $filters = $this->getTable()->getFilters();

        foreach ($filters as $filterName => $filter) {
            $this->removeTableFilter(
                $filterName,
                shouldTriggerUpdatedFiltersHook: false,
            );
        }

        $this->updatedTableFilters();

        $this->resetTableSearch();
        $this->resetTableColumnSearches();
    }

    public function resetTableFiltersForm(): void
    {
        $this->getTableFiltersForm()->fill();

        $this->updatedTableFilters();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $data = $this->getTableFiltersForm()->getRawState();

        foreach ($this->getTable()->getFilters() as $filter) {
            $filter->applyToBaseQuery(
                $query,
                $data[$filter->getName()] ?? [],
            );
        }

        return $query->where(function (Builder $query) use ($data) {
            foreach ($this->getTable()->getFilters() as $filter) {
                $filter->apply(
                    $query,
                    $data[$filter->getName()] ?? [],
                );
            }
        });
    }

    public function getTableFilterState(string $name): ?array
    {
        return $this->getTableFiltersForm()->getRawState()[$this->parseTableFilterName($name)] ?? null;
    }

    public function parseTableFilterName(string $name): string
    {
        if (! class_exists($name)) {
            return $name;
        }

        if (! is_subclass_of($name, BaseFilter::class)) {
            return $name;
        }

        return $name::getDefaultName();
    }

    /**
     * @return array<string, Forms\Components\Group>
     */
    public function getTableFiltersFormSchema(): array
    {
        $schema = [];

        foreach ($this->getTable()->getFilters() as $filter) {
            $schema[$filter->getName()] = Forms\Components\Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filter->getName())
                ->columnSpan($filter->getColumnSpan())
                ->columnStart($filter->getColumnStart())
                ->columns($filter->getColumns());
        }

        return $schema;
    }

    public function getTableFiltersSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_filters";
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<BaseFilter>
     */
    protected function getTableFilters(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableFiltersFormWidth(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableFiltersFormMaxHeight(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableFiltersInSession(): bool
    {
        return false;
    }
}
