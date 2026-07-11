<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;

class FilterPanel extends Component
{
    protected string $evaluationIdentifier = 'filterPanel';

    /**
     * @var array<BaseFilter>
     */
    protected array $filters = [];

    protected int | array | Closure | null $columns = null;

    protected Width | string | Closure | null $width = null;

    protected string | Closure | null $maxHeight = null;

    protected FiltersResetActionPosition | Closure | null $resetActionPosition = null;

    protected ?Closure $modifyTriggerActionUsing = null;

    /**
     * @param  array<BaseFilter>  $filters
     */
    final public function __construct(protected FiltersLayout $location, array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public static function make(FiltersLayout $location, array $filters = []): static
    {
        $static = app(static::class, ['location' => $location, 'filters' => $filters]);
        $static->configure();

        return $static;
    }

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
}
