<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

trait HasTooltip
{
    protected string | Htmlable | Closure | null $tooltip = null;

    protected string | Htmlable | Closure | null $headerTooltip = null;

    protected string | Htmlable | Closure | null $emptyTooltip = null;

    public function tooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(mixed $state = null, ?Model $relationshipRecord = null): string | Htmlable | null
    {
        return $this->evaluateForStateItem($this->tooltip, $state, $relationshipRecord);
    }

    public function headerTooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->headerTooltip = $tooltip;

        return $this;
    }

    public function getHeaderTooltip(): string | Htmlable | null
    {
        return $this->evaluate($this->headerTooltip);
    }

    public function emptyTooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->emptyTooltip = $tooltip;

        return $this;
    }

    public function getEmptyTooltip(): string | Htmlable | null
    {
        return $this->evaluate($this->emptyTooltip);
    }

    public function hasTooltip(): bool
    {
        return $this->tooltip !== null;
    }
}
