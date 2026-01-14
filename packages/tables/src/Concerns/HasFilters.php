<?php

namespace Filament\Tables\Concerns;

use Filament\Facades\Filament;
use Filament\QueryBuilder\Forms\Components\RuleBuilder;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @property-read Schema $tableFiltersForm
 */
trait HasFilters
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $tableFilters = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $tableDeferredFilters = null;

    public function getTableFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('tableFiltersForm')) {
            return $this->getSchema('tableFiltersForm');
        }

        $table = $this->getTable();

        return $this->makeSchema()
            ->columns($table->getFiltersFormColumns())
            ->model($table->getModel())
            ->schema($table->getFiltersFormSchema())
            ->when(
                $table->hasDeferredFilters(),
                fn (Schema $schema) => $schema
                    ->statePath('tableDeferredFilters')
                    ->partiallyRender(),
                fn (Schema $schema) => $schema
                    ->statePath('tableFilters')
                    ->live(),
            );
    }

    public function updatedTableFilters(): void
    {
        if ($this->getTable()->hasDeferredFilters()) {
            $this->tableDeferredFilters = $this->tableFilters;
        }

        $this->handleTableFilterUpdates();
    }

    protected function handleTableFilterUpdates(): void
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

    public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
    {
        $filter = $this->getTable()->getFilter($filterName);
        $filterResetState = $filter->getResetState();

        $filterFormGroup = $this->getTableFiltersForm()->getComponentByStatePath($filterName);

        if (($filter instanceof QueryBuilder) && blank($field)) {
            $filterFormGroup->getChildSchema()->fill();
        } elseif ($filter instanceof QueryBuilder) {
            $ruleBuilder = $filterFormGroup?->getChildSchema()->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

            $ruleBuilderRawState = $ruleBuilder?->getRawState() ?? [];
            unset($ruleBuilderRawState[$field]);
            $ruleBuilder?->rawState($ruleBuilderRawState);
        } else {
            $filterFields = $filterFormGroup?->getChildSchema()->getFlatFields() ?? [];

            if (filled($field) && array_key_exists($field, $filterFields)) {
                $filterFields = [$field => $filterFields[$field]];
            }

            foreach ($filterFields as $fieldName => $field) {
                $state = $field->getState();

                $field->state($filterResetState[$fieldName] ?? match (true) {
                    is_array($state) => [],
                    is_bool($state) => $field->hasNullableBooleanState() ? null : false,
                    default => null,
                });
            }
        }

        if ($isRemovingAllFilters) {
            return;
        }

        if ($this->getTable()->hasDeferredFilters()) {
            $this->applyTableFilters();

            return;
        }

        $this->handleTableFilterUpdates();
    }

    public function removeTableFilters(): void
    {
        $filters = $this->getTable()->getFilters();

        foreach ($filters as $filterName => $filter) {
            if (collect($filter->getIndicators())->every(fn (Indicator $indicator): bool => $indicator->isRemovable())) {
                $this->removeTableFilter(
                    $filterName,
                    isRemovingAllFilters: true,
                );
            }
        }

        $this->resetTableSearch();
        $this->resetTableColumnSearches();

        if ($this->getTable()->hasDeferredFilters()) {
            $this->applyTableFilters();

            return;
        }

        $this->handleTableFilterUpdates();
    }

    public function resetTableFiltersForm(): void
    {
        $this->getTableFiltersForm()->fill();

        if ($this->getTable()->hasDeferredFilters()) {
            $this->applyTableFilters();

            return;
        }

        $this->handleTableFilterUpdates();
    }

    public function applyTableFilters(): void
    {
        $this->tableFilters = $this->tableDeferredFilters;

        $this->handleTableFilterUpdates();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $table = $this->getTable();

        if ($table->hasDeferredFilters()) {
            $this->getTableFiltersForm()->statePath('tableFilters')->flushCachedAbsoluteStatePaths();
        }

        try {
            foreach ($table->getFilters() as $filter) {
                $filter->applyToBaseQuery(
                    $query,
                    $this->getTableFilterState($filter->getName()) ?? [],
                );
            }

            return $query->where(function (Builder $query) use ($table): void {
                foreach ($table->getFilters() as $filter) {
                    $filter->apply(
                        $query,
                        $this->getTableFilterState($filter->getName()) ?? [],
                    );
                }
            });
        } finally {
            if ($table->hasDeferredFilters()) {
                $this->getTableFiltersForm()->statePath('tableDeferredFilters')->flushCachedAbsoluteStatePaths();
            }
        }
    }

    public function getTableFilterState(string $name): ?array
    {
        return Arr::get($this->tableFilters, $this->parseTableFilterName($name));
    }

    public function getTableFilterFormState(string $name): ?array
    {
        return Arr::get($this->getTable()->hasDeferredFilters() ? $this->tableDeferredFilters : $this->tableFilters, $this->parseTableFilterName($name));
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

    public function getTableFiltersSessionKey(): string
    {
        $namespace = $this::class;

        $tenantKey = null;

        if (class_exists(Filament::class)) {
            $tenantKey = Filament::getTenant()?->getKey();
        }

        if (filled($tenantKey)) {
            $namespace .= '|' . $tenantKey;
        }

        $table = md5($namespace);

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
