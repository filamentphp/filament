<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Tables\Filters\Indicator;
use Illuminate\Support\Arr;

trait HasIndicators
{
    protected string | Closure | null $indicateUsing = null;

    protected Indicator | string | Closure | null $indicator = null;

    /**
     * @var array<Indicator> | Closure
     */
    protected array | Closure $indicators = [];

    public function indicator(Indicator | string | Closure | null $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * @param  array<Indicator> | Closure  $indicators
     */
    public function indicators(array | Closure $indicators): static
    {
        $this->indicators = $indicators;

        return $this;
    }

    public function indicateUsing(?Closure $callback): static
    {
        return $this->indicators($callback);
    }

    /**
     * @return array<Indicator> | array<string>
     */
    public function getIndicators(): array
    {
        $state = $this->getState();

        $indicators = $this->evaluate($this->indicators, [
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

    public function getIndicator(): Indicator | string
    {
        $state = $this->getState();

        return $this->evaluate($this->indicator, [
            'data' => $state,
            'state' => $state,
        ]) ?? $this->getLabel();
    }
}
