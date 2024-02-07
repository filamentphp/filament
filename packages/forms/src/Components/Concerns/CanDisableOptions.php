<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Collection;

trait CanDisableOptions
{
    protected bool | Closure | null $isOptionDisabled = null;

    public function disableOptionWhen(bool | Closure $callback): static
    {
        $this->isOptionDisabled = $callback;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getEnabledOptions(): array
    {
        return collect($this->getOptions())
            ->reduce(function (Collection $carry, $label, $value): Collection {
                if (is_array($label)) {
                    return $carry->merge($label);
                }

                return $carry->put($value, $label);
            }, collect())
            ->filter(fn ($label, $value) => ! $this->isOptionDisabled($value, $label))
            ->all();
    }

    /**
     * @param  array-key  $value
     */
    public function isOptionDisabled($value, string $label): bool
    {
        if ($this->isOptionDisabled === null) {
            return false;
        }

        return (bool) $this->evaluate($this->isOptionDisabled, [
            'label' => $label,
            'value' => $value,
        ]);
    }

    public function hasDynamicDisabledOptions(): bool
    {
        return $this->isOptionDisabled instanceof Closure;
    }
}
