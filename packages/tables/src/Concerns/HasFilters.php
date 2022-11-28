<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Layout;
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
            return $this->getCachedForm('tableFiltersForm');
        }

        return $this->makeForm()
            ->schema($this->getTableFiltersFormSchema())
            ->columns($this->getTable()->getFiltersFormColumns())
            ->statePath('tableFilters')
            ->reactive();
    }

    public function updatedTableFilters(): void
    {
        if ($this->getTable()->persistsFiltersInSession()) {
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
        return $this->getTableFiltersForm()->getRawState()[$this->parseFilterName($name)] ?? null;
    }

    public function parseFilterName(string $name): string
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
     *
     * @return int | array<string, int | null>
     */
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
    protected function getTableFiltersLayout(): ?string
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
