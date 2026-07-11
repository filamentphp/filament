<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\View\TablesIconAlias;
use LogicException;

trait HasFilters
{
    /**
     * @var array<string, BaseFilter>
     */
    protected array $filters = [];

    /**
     * @var array<string, FilterPanel>
     */
    protected array $filterPanels = [];

    protected bool $hasFilterPanels = false;

    protected ?Closure $filtersFormSchema = null;

    /**
     * @var int | array<string, int | null> | Closure
     */
    protected int | array | Closure | null $filtersFormColumns = null;

    protected string | Closure | null $filtersFormMaxHeight = null;

    protected Width | string | Closure | null $filtersFormWidth = null;

    protected FiltersLayout | Closure | null $filtersLayout = null;

    protected ?Closure $modifyFiltersTriggerActionUsing = null;

    protected bool | Closure | null $persistsFiltersInSession = false;

    protected bool | Closure $shouldDeselectAllRecordsWhenFiltered = true;

    protected bool | Closure $hasDeferredFilters = true;

    protected ?Closure $modifyFiltersApplyActionUsing = null;

    protected ?Closure $modifyFiltersRemoveAllActionUsing = null;

    protected FiltersResetActionPosition | Closure | null $filtersResetActionPosition = null;

    public function deferFilters(bool | Closure $condition = true): static
    {
        $this->hasDeferredFilters = $condition;

        return $this;
    }

    public function hasDeferredFilters(): bool
    {
        return (bool) $this->evaluate($this->hasDeferredFilters);
    }

    public function filtersApplyAction(?Closure $callback): static
    {
        $this->modifyFiltersApplyActionUsing = $callback;

        return $this;
    }

    public function filtersRemoveAllAction(?Closure $callback): static
    {
        $this->modifyFiltersRemoveAllActionUsing = $callback;

        return $this;
    }

    public function deselectAllRecordsWhenFiltered(bool | Closure $condition = true): static
    {
        $this->shouldDeselectAllRecordsWhenFiltered = $condition;

        return $this;
    }

    /**
     * @param  array<BaseFilter | FilterPanel>  $filters
     */
    public function filters(array $filters, FiltersLayout | string | Closure | null $layout = null): static
    {
        $this->filters = [];
        $this->filterPanels = [];
        $this->hasFilterPanels = false;
        $this->pushFilters($filters);

        if ($layout) {
            $this->filtersLayout($layout);
        }

        return $this;
    }

    /**
     * @param  array<BaseFilter | FilterPanel>  $filters
     */
    public function pushFilters(array $filters): static
    {
        $incomingArePanels = null;

        foreach ($filters as $filter) {
            $isPanel = $filter instanceof FilterPanel;

            $incomingArePanels ??= $isPanel;

            if ($isPanel !== $incomingArePanels) {
                throw new LogicException('A table\'s [filters()] must be either all [FilterPanel] instances or all filters, not a mix.');
            }
        }

        if ($incomingArePanels === null) {
            return $this;
        }

        if ((filled($this->filters) || filled($this->filterPanels)) && ($this->hasFilterPanels !== $incomingArePanels)) {
            throw new LogicException('A table cannot mix [FilterPanel] instances with loose filters, including across [pushFilters()] calls.');
        }

        $this->hasFilterPanels = $incomingArePanels;

        if (! $incomingArePanels) {
            foreach ($filters as $filter) {
                $filter->table($this);

                $this->filters[$filter->getName()] = $filter;
            }

            return $this;
        }

        foreach ($filters as $panel) {
            $locationName = $panel->getLocation()->name;

            if (array_key_exists($locationName, $this->filterPanels)) {
                throw new LogicException("A table can only have one filter panel per location; [{$locationName}] is used more than once.");
            }

            $this->filterPanels[$locationName] = $panel;

            foreach ($panel->getFilters() as $filter) {
                $filter->table($this);

                $this->filters[$filter->getName()] = $filter;
            }
        }

        return $this;
    }

    public function hasFilterPanels(): bool
    {
        return $this->hasFilterPanels;
    }

    /**
     * @return array<FilterPanel>
     */
    public function getFilterPanels(): array
    {
        if (! $this->hasFilterPanels) {
            return [
                FilterPanel::make($this->getFiltersLayout(), array_values($this->getFilters())),
            ];
        }

        return array_values(array_filter(
            $this->filterPanels,
            fn (FilterPanel $panel): bool => (bool) array_filter(
                $panel->getFilters(),
                fn (BaseFilter $filter): bool => $filter->isVisible(),
            ),
        ));
    }

    /**
     * @param  int | array<string, int | null> | Closure  $columns
     */
    public function filtersFormColumns(int | array | Closure | null $columns): static
    {
        $this->filtersFormColumns = $columns;

        return $this;
    }

    public function filtersFormMaxHeight(string | Closure | null $height): static
    {
        $this->filtersFormMaxHeight = $height;

        return $this;
    }

    public function filtersFormWidth(Width | string | Closure | null $width): static
    {
        $this->filtersFormWidth = $width;

        return $this;
    }

    public function filtersResetActionPosition(FiltersResetActionPosition | Closure | null $position): static
    {
        $this->filtersResetActionPosition = $position;

        return $this;
    }

    public function getFiltersResetActionPosition(): FiltersResetActionPosition
    {
        return $this->evaluate($this->filtersResetActionPosition) ?? FiltersResetActionPosition::Header;
    }

    public function getResetActionPositionForPanel(FilterPanel $panel): FiltersResetActionPosition
    {
        return $panel->getResetActionPosition() ?? $this->getFiltersResetActionPosition();
    }

    public function filtersLayout(FiltersLayout | Closure | null $filtersLayout): static
    {
        $this->filtersLayout = $filtersLayout;

        return $this;
    }

    public function filtersTriggerAction(?Closure $callback): static
    {
        $this->modifyFiltersTriggerActionUsing = $callback;

        return $this;
    }

    public function persistFiltersInSession(bool | Closure $condition = true): static
    {
        $this->persistsFiltersInSession = $condition;

        return $this;
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getFilters(bool $withHidden = false): array
    {
        if ($withHidden) {
            return $this->filters;
        }

        return array_filter(
            $this->filters,
            fn (BaseFilter $filter): bool => $filter->isVisible(),
        );
    }

    public function getFilter(string $name, bool $withHidden = false): ?BaseFilter
    {
        return $this->getFilters($withHidden)[$name] ?? null;
    }

    public function getFiltersForm(): Schema
    {
        return $this->getLivewire()->getTableFiltersForm();
    }

    public function getFiltersFormForPanel(FilterPanel $panel): ?Schema
    {
        $form = $this->getFiltersForm();

        // Single panel => the whole flat form renders as one.
        if (count($this->getFilterPanels()) <= 1) {
            return $form;
        }

        foreach ($form->getComponents(withHidden: true) as $component) {
            if ($component->getKey(isAbsolute: false) === 'filterPanel::' . $panel->getLocation()->name) {
                return $component->getChildSchema();
            }
        }

        return null;
    }

    public function filtersFormSchema(?Closure $schema): static
    {
        $this->filtersFormSchema = $schema;

        return $this;
    }

    /**
     * @return array<string, Group>
     */
    public function getFiltersFormSchema(): array
    {
        if ($this->filtersFormSchema && $this->hasFilterPanels) {
            throw new LogicException('[filtersFormSchema()] cannot be combined with [FilterPanel] instances; use a flat filters array to customize the whole schema, or lay filters out within each panel.');
        }

        $filterGroups = [];

        foreach ($this->getFilters() as $filterName => $filter) {
            $filterGroups[$filterName] = Group::make()
                ->schema($filter->getSchemaComponents())
                ->statePath($filterName)
                ->key($filterName)
                ->columnSpan($filter->getColumnSpan())
                ->columnStart($filter->getColumnStart())
                ->columns($filter->getColumns());
        }

        if ($this->filtersFormSchema) {
            return $this->evaluate($this->filtersFormSchema, ['filters' => $filterGroups]) ?? array_values($filterGroups);
        }

        $panels = $this->getFilterPanels();

        // A single panel (including the normalised implicit one for flat `filters()`) renders as a flat schema, identical to before.
        if (count($panels) <= 1) {
            return array_values($filterGroups);
        }

        $containers = [];

        foreach ($panels as $panel) {
            $panelFilterGroups = array_values(array_intersect_key(
                $filterGroups,
                array_flip(array_map(fn (BaseFilter $filter): string => $filter->getName(), $panel->getFilters())),
            ));

            if ($panelFilterGroups === []) {
                continue;
            }

            $containers[] = Group::make()
                ->schema($panelFilterGroups)
                ->key('filterPanel::' . $panel->getLocation()->name)
                ->columns($this->getFiltersFormColumnsForPanel($panel));
        }

        return $containers;
    }

    public function getFiltersTriggerAction(): Action
    {
        $action = Action::make('openFilters')
            ->label(__('filament-tables::table.actions.filter.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::ACTIONS_FILTER) ?? Heroicon::Funnel)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->modalSubmitAction(false)
            ->extraModalFooterActions([
                $this->getFiltersApplyAction()
                    ->close(),
                Action::make('resetFilters')
                    ->label(__('filament-tables::table.filters.actions.reset.label'))
                    ->color('danger')
                    ->action('resetTableFiltersForm')
                    ->button(),
            ])
            ->modalCancelActionLabel(__('filament::components/modal.actions.close.label'))
            ->table($this)
            ->authorize(true);

        if ($this->modifyFiltersTriggerActionUsing) {
            $action = $this->evaluate($this->modifyFiltersTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        $action->extraAttributes(['class' => 'fi-force-enabled'], merge: true);

        return $action;
    }

    public function getFiltersApplyAction(): Action
    {
        $action = Action::make('applyFilters')
            ->label(__('filament-tables::table.filters.actions.apply.label'))
            ->action('applyTableFilters')
            ->table($this)
            ->visible($this->hasDeferredFilters())
            ->authorize(true)
            ->button();

        if ($this->modifyFiltersApplyActionUsing) {
            $action = $this->evaluate($this->modifyFiltersApplyActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function getFiltersRemoveAllAction(): Action
    {
        $action = Action::make('removeAllFilters')
            ->label(__('filament-tables::table.filters.actions.remove_all.label'))
            ->tooltip(__('filament-tables::table.filters.actions.remove_all.tooltip'))
            ->action('removeTableFilters')
            ->livewireTarget('removeTableFilters,removeTableFilter')
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::FILTERS_REMOVE_ALL_BUTTON) ?? Heroicon::XMark)
            ->color('gray')
            ->defaultSize(Size::Small)
            ->table($this)
            ->authorize(true);

        if ($this->modifyFiltersRemoveAllActionUsing) {
            $action = $this->evaluate($this->modifyFiltersRemoveAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    /**
     * @return int | array<string, int | null>
     */
    /**
     * @return int | array<string, int | null>
     */
    public function getFiltersFormColumns(): int | array
    {
        return $this->getFiltersFormColumnsForPanel($this->getFilterPanels()[0]);
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getFiltersFormColumnsForPanel(FilterPanel $panel): int | array
    {
        return $panel->getColumns() ?? $this->getFiltersFormColumnsForLocation($panel->getLocation());
    }

    /**
     * @return int | array<string, int | null>
     */
    protected function getFiltersFormColumnsForLocation(FiltersLayout $location): int | array
    {
        return $this->evaluate($this->filtersFormColumns) ?? match ($location) {
            FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible, FiltersLayout::BelowContent => [
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ],
            default => 1,
        };
    }

    public function getFiltersFormMaxHeight(): ?string
    {
        return $this->evaluate($this->filtersFormMaxHeight);
    }

    public function getFiltersFormMaxHeightForPanel(FilterPanel $panel): ?string
    {
        return $panel->getMaxHeight() ?? $this->evaluate($this->filtersFormMaxHeight);
    }

    public function getFiltersFormWidth(): Width | string | null
    {
        return $this->getFiltersFormWidthForPanel($this->getFilterPanels()[0]);
    }

    public function getFiltersFormWidthForPanel(FilterPanel $panel): Width | string | null
    {
        return $panel->getWidth() ?? $this->evaluate($this->filtersFormWidth) ?? match ($this->getFiltersFormColumnsForPanel($panel)) {
            2 => Width::TwoExtraLarge,
            3 => Width::FourExtraLarge,
            4 => Width::SixExtraLarge,
            default => null,
        };
    }

    public function getFiltersLayout(): FiltersLayout
    {
        return $this->evaluate($this->filtersLayout) ?? FiltersLayout::Dropdown;
    }

    public function isFilterable(): bool
    {
        return (bool) count($this->getFilters());
    }

    public function persistsFiltersInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsFiltersInSession);
    }

    public function shouldDeselectAllRecordsWhenFiltered(): bool
    {
        return (bool) $this->evaluate($this->shouldDeselectAllRecordsWhenFiltered);
    }

    public function getActiveFiltersCount(): int
    {
        return array_reduce(
            $this->getFilters(),
            fn (int $carry, BaseFilter $filter): int => $carry + $filter->getActiveCount(),
            0,
        );
    }

    public function getActiveFiltersCountForPanel(FilterPanel $panel): int
    {
        return array_reduce(
            $panel->getFilters(),
            fn (int $carry, BaseFilter $filter): int => $carry + $filter->getActiveCount(),
            0,
        );
    }

    public function isFiltered(): bool
    {
        return $this->getActiveFiltersCount() > 0;
    }
}
