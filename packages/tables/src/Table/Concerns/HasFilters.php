<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\BaseFilter;

trait HasFilters
{
    /**
     * @var array<string, BaseFilter>
     */
    protected array $filters = [];

    protected ?Closure $filtersFormSchema = null;

    /**
     * @var int | array<string, int | null> | Closure
     */
    protected int | array | Closure | null $filtersFormColumns = null;

    protected string | Closure | null $filtersFormMaxHeight = null;

    protected MaxWidth | string | Closure | null $filtersFormWidth = null;

    protected FiltersLayout | Closure | null $filtersLayout = null;

    protected ?Closure $modifyFiltersTriggerActionUsing = null;

    protected bool | Closure | null $persistsFiltersInSession = false;

    protected bool | Closure $shouldDeselectAllRecordsWhenFiltered = true;

    protected bool | Closure $hasDeferredFilters = false;

    protected ?Closure $modifyFiltersApplyActionUsing = null;

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

    public function deselectAllRecordsWhenFiltered(bool | Closure $condition = true): static
    {
        $this->shouldDeselectAllRecordsWhenFiltered = $condition;

        return $this;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public function filters(array $filters, FiltersLayout | string | Closure | null $layout = null): static
    {
        $this->filters = [];
        $this->pushFilters($filters);

        if ($layout) {
            $this->filtersLayout($layout);
        }

        return $this;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public function pushFilters(array $filters): static
    {
        foreach ($filters as $filter) {
            $filter->table($this);

            $this->filters[$filter->getName()] = $filter;
        }

        return $this;
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

    public function filtersFormWidth(MaxWidth | string | Closure | null $width): static
    {
        $this->filtersFormWidth = $width;

        return $this;
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
    public function getFilters(): array
    {
        return array_filter(
            $this->filters,
            fn (BaseFilter $filter): bool => $filter->isVisible(),
        );
    }

    public function getFilter(string $name): ?BaseFilter
    {
        return $this->getFilters()[$name] ?? null;
    }

    public function getFiltersForm(): Form
    {
        return $this->getLivewire()->getTableFiltersForm();
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
        $filters = [];

        foreach ($this->getFilters() as $filterName => $filter) {
            $filters[$filterName] = Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filterName)
                ->key($filterName)
                ->columnSpan($filter->getColumnSpan())
                ->columnStart($filter->getColumnStart())
                ->columns($filter->getColumns());
        }

        return $this->evaluate($this->filtersFormSchema, ['filters' => $filters]) ?? array_values($filters);
    }

    public function getFiltersTriggerAction(): Action
    {
        $action = Action::make('openFilters')
            ->label(__('filament-tables::table.actions.filter.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve('tables::actions.filter') ?? 'heroicon-m-funnel')
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
            ->table($this);

        if ($this->modifyFiltersTriggerActionUsing) {
            $action = $this->evaluate($this->modifyFiltersTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(ActionSize::Small);
        }

        return $action;
    }

    public function getFiltersApplyAction(): Action
    {
        $action = Action::make('applyFilters')
            ->label(__('filament-tables::table.filters.actions.apply.label'))
            ->action('applyTableFilters')
            ->table($this)
            ->visible($this->hasDeferredFilters())
            ->button();

        if ($this->modifyFiltersApplyActionUsing) {
            $action = $this->evaluate($this->modifyFiltersApplyActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getFiltersFormColumns(): int | array
    {
        return $this->evaluate($this->filtersFormColumns) ?? match ($this->getFiltersLayout()) {
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

    public function getFiltersFormWidth(): MaxWidth | string | null
    {
        return $this->evaluate($this->filtersFormWidth) ?? match ($this->getFiltersFormColumns()) {
            2 => MaxWidth::TwoExtraLarge,
            3 => MaxWidth::FourExtraLarge,
            4 => MaxWidth::SixExtraLarge,
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

    public function isFiltered(): bool
    {
        return $this->getActiveFiltersCount() > 0;
    }
}
