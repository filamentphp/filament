<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

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
        return array_filter(
            $this->getOptions(),
            fn ($label, $value) => ! $this->isOptionDisabled($value, $label),
            ARRAY_FILTER_USE_BOTH,
        );
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
}
