<?php

namespace Filament\Tables\Columns\Concerns;

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
            ->when($this->queriesRelationships(), fn ($label) => $label->before('.'), fn ($label) => $label->after('.'))
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }
}
