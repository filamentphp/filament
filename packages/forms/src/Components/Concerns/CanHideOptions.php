<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait CanHideOptions
{
    /**
     * @var array<bool | Closure>
     */
    protected array $isOptionHidden = [];

    public function hideOptionWhen(bool | Closure | null $callback, bool $merge = false): static
    {
        if ($merge) {
            $this->isOptionHidden[] = $callback;
        } else {
            $this->isOptionHidden = Arr::wrap($callback);
        }

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getVisibleOptions(): array
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
            ->filter(fn ($label, $value) => ! $this->isOptionHidden($value, $label))
            ->all();
    }

    /**
     * @param  array-key  $value
     */
    public function isOptionHidden($value, string $label): bool
    {
        return collect($this->isOptionHidden)
            ->contains(fn (bool | Closure $isOptionHidden): bool => (bool) $this->evaluate($isOptionHidden, [
                'label' => $label,
                'value' => $value,
            ]));
    }

    public function hasDynamicHiddenOptions(): bool
    {
        return collect($this->isOptionHidden)
            ->contains(fn (bool | Closure $isOptionHidden): bool => $isOptionHidden instanceof Closure);
    }
}
