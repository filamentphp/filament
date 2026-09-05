<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Enums\Width;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Table;

class FilterPanel extends Component
{
    protected string $evaluationIdentifier = 'filterPanel';

    protected Table $table;

    /**
     * @var array<BaseFilter>
     */
    protected array $filters = [];

    /**
     * @var int | array<string, int | null> | Closure | null
     */
    protected int | array | Closure | null $columns = null;

    protected Width | string | Closure | null $width = null;

    protected string | Closure | null $maxHeight = null;

    protected FiltersResetActionPosition | Closure | null $resetActionPosition = null;

    protected ?Closure $modifyTriggerActionUsing = null;

    /**
     * @param  array<BaseFilter>  $filters
     */
    final public function __construct(protected FiltersLayout $location, array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public static function make(FiltersLayout $location, array $filters): static
    {
        $static = app(static::class, ['location' => $location, 'filters' => $filters]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public function pushFilters(array $filters): static
    {
        $this->filters = [
            ...$this->filters,
            ...$filters,
        ];

        return $this;
    }

    public function table(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param  int | array<string, int | null> | Closure | null  $columns
     */
    public function columns(int | array | Closure | null $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function width(Width | string | Closure | null $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function maxHeight(string | Closure | null $maxHeight): static
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function resetActionPosition(FiltersResetActionPosition | Closure | null $position): static
    {
        $this->resetActionPosition = $position;

        return $this;
    }

    public function triggerAction(?Closure $callback): static
    {
        $this->modifyTriggerActionUsing = $callback;

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getLivewire(): HasTable
    {
        return $this->getTable()->getLivewire();
    }

    public function getLocation(): FiltersLayout
    {
        return $this->location;
    }

    /**
     * @return array<BaseFilter>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<BaseFilter>
     */
    public function getVisibleFilters(): array
    {
        return array_values(array_filter(
            $this->filters,
            fn (BaseFilter $filter): bool => $filter->isVisible(),
        ));
    }

    /**
     * An interactive panel owns a trigger that opens it, so a table may only have one of them.
     * The non-collapsible before/after locations count too, as they open from the filter trigger
     * as floating panels on mobile.
     */
    public function isInteractive(): bool
    {
        return in_array($this->location, [
            FiltersLayout::Dropdown,
            FiltersLayout::Modal,
            FiltersLayout::AboveContentCollapsible,
            FiltersLayout::BeforeContent,
            FiltersLayout::BeforeContentCollapsible,
            FiltersLayout::AfterContent,
            FiltersLayout::AfterContentCollapsible,
        ], strict: true);
    }

    public function isCollapsible(): bool
    {
        return in_array($this->location, [
            FiltersLayout::AboveContentCollapsible,
            FiltersLayout::BeforeContentCollapsible,
            FiltersLayout::AfterContentCollapsible,
        ], strict: true);
    }

    /**
     * @return int | array<string, int | null> | null
     */
    public function getColumns(): int | array | null
    {
        return $this->evaluate($this->columns);
    }

    public function getWidth(): Width | string | null
    {
        return $this->evaluate($this->width);
    }

    public function getMaxHeight(): ?string
    {
        return $this->evaluate($this->maxHeight);
    }

    public function getResetActionPosition(): ?FiltersResetActionPosition
    {
        return $this->evaluate($this->resetActionPosition);
    }

    public function getModifyTriggerActionUsing(): ?Closure
    {
        return $this->modifyTriggerActionUsing;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
