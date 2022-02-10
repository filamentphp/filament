<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

trait HasOptions
{
    protected array | Arrayable | string | Closure | null $options = null;

    public function options(array | Arrayable | string | Closure | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if ($options === null) {
            $options = $this->queriesRelationships() ? $this->getRelationshipOptions() : [];
        }

        if (is_string($options) && function_exists('enum_exists') && enum_exists($options)) {
            $options = collect($options::cases())->mapWithKeys(fn ($case) => [($case?->value ?? $case->name) => $case->name]);
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }
}
