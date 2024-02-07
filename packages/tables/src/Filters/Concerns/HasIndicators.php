<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Tables\Filters\Indicator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

trait HasIndicators
{
    protected string | Closure | null $indicateUsing = null;

    protected Indicator | string | Htmlable | Closure | null $indicator = null;

    public function indicator(Indicator | string | Htmlable | Closure | null $indicator): static
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
     * @return array<Indicator>
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

        $indicators = Arr::wrap($indicators);

        foreach ($indicators as $field => $indicator) {
            if (! $indicator instanceof Indicator) {
                $indicator = Indicator::make($indicator);
            }

            if (is_string($field)) {
                $indicator = $indicator->removeField($field);
            }

            $indicators[$field] = $indicator;
        }

        return $indicators;
    }

    public function getIndicator(): Indicator | string | Htmlable
    {
        $state = $this->getState();

        return $this->evaluate($this->indicator, [
            'data' => $state,
            'state' => $state,
        ]) ?? $this->getLabel();
    }
}
