<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
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
            ->when(
                $this->hasDisabledOptions(),
                fn (Collection $options): Collection => $options->filter(fn ($label, $value) => ! $this->isOptionDisabled($value, $label)),
            )
            ->all();
    }

    /**
     * @param  array-key  $value
     */
    public function isOptionDisabled($value, string | Htmlable $label): bool
    {
        foreach ($this->isOptionDisabled as $isOptionDisabled) {
            if ($this->evaluate($isOptionDisabled, [
                'label' => $label,
                'value' => $value,
            ])) {
                return true;
            }
        }

        return false;
    }

    public function hasDisabledOptions(): bool
    {
        foreach ($this->isOptionDisabled as $isOptionDisabled) {
            if ($isOptionDisabled !== false) {
                return true;
            }
        }

        return false;
    }

    public function hasDynamicDisabledOptions(): bool
    {
        foreach ($this->isOptionDisabled as $isOptionDisabled) {
            if ($isOptionDisabled instanceof Closure) {
                return true;
            }
        }

        return false;
    }
}
