<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait CanDisableOptions
{
    /**
     * @var array<bool | Closure>
     */
    protected array $isOptionDisabled = [];

    public function disableOptionWhen(bool | Closure | null $callback, bool $merge = false): static
    {
        if ($merge) {
            $this->isOptionDisabled[] = $callback;
        } else {
            $this->isOptionDisabled = Arr::wrap($callback);
        }

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
                    foreach ($label as $key => $value) {
                        $carry->put($key, $value);
                    }

                    return $carry;
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
        return collect($this->isOptionDisabled)
            ->contains(fn (bool | Closure $isOptionDisabled): bool => (bool) $this->evaluate($isOptionDisabled, [
                'label' => $label,
                'value' => $value,
            ]));
    }

    public function hasDynamicDisabledOptions(): bool
    {
        return collect($this->isOptionDisabled)
            ->contains(fn (bool | Closure $isOptionDisabled): bool => $isOptionDisabled instanceof Closure);
    }
}
