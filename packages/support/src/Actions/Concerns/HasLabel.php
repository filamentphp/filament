<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Support\Str;

trait HasLabel
{
    protected string | Closure | null $label = null;

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? (string) Str::of($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }
}
