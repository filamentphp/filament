<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasHeader
{
    protected string | Htmlable | Closure | null $heading = null;

    protected View | Htmlable | Closure | null $header = null;

    protected string | Htmlable | Closure | null $description = null;

    public function description(string | Htmlable | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function header(View | Htmlable | Closure | null $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function heading(string | Htmlable | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }

    public function hasHeader(): bool
    {
        return filled($this->getHeader())
            || filled($this->getHeading())
            || filled($this->getDescription())
            || ($this->getVisibleHeaderActions() && (! $this->isReordering()))
            || $this->isReorderable()
            || $this->areGroupingSettingsVisible()
            || $this->isSearchable()
            || $this->isFilterable()
            || count($this->getFilterIndicators())
            || $this->hasColumnManager();
    }

    public function hasHeaderToolbar(): bool
    {
        return $this->isReorderable()
            || $this->areGroupingSettingsVisible()
            || $this->isSearchable()
            || $this->hasFiltersTrigger()
            || $this->hasColumnManager();
    }

    /**
     * Whether the header is always visible, visible only while records are selected, or hidden.
     *
     * @see https://github.com/filamentphp/filament/pull/19787
     */
    public function getHeaderVisibilityMode(): string
    {
        return $this->getVisibilityMode($this->hasHeader());
    }

    public function getHeaderToolbarVisibilityMode(): string
    {
        return $this->getVisibilityMode($this->hasHeaderToolbar());
    }

    protected function getVisibilityMode(bool $hasContent): string
    {
        if ($hasContent || $this->hasNonBulkToolbarAction()) {
            return 'visible';
        }

        return count($this->getVisibleToolbarActions()) ? 'selection' : 'hidden';
    }
}
