<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait HasIndicators
{
    protected string | Closure | null $indicateUsing = null;

    protected string | Closure | null $indicator = null;

    public function indicator(string | Closure | null $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function indicateUsing(?Closure $callback): static
    {
        $this->indicateUsing = $callback;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getIndicators(): array
    {
        $state = $this->getState();

        $indicators = $this->evaluate($this->indicateUsing, [
            'data' => $state,
            'state' => $state,
        ]);

        if (blank($indicators)) {
            return [];
        }

        return Arr::wrap($indicators);
    }

    public function getIndicator(): string
    {
        $state = $this->getState();

        return $this->evaluate($this->indicator, [
            'data' => $state,
            'state' => $state,
        ]) ?? $this->getLabel();
    }
}
