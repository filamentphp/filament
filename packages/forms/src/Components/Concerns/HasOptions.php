<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

trait HasOptions
{
    protected string |array | Arrayable | Closure $options = [];

    public function options(string |array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if (is_string($options)) {
            $options = collect($options::cases())
                ->mapWithKeys(function ($eachEnumInstance) {
                    return [$eachEnumInstance?->value ?? $eachEnumInstance->name => $eachEnumInstance->name];
                })
                ->toArray();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }
}
