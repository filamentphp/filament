<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Str;

trait HasLabel
{
    protected string | Closure | null $label = null;
    protected bool | Closure $isLabelLocalized = false;

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }
    public function localizeLabel(bool | Closure $condition = true): static
    {
        $this->isLabelLocalized = $condition;

        return $this;
    }
    public function isLabelLocalized(): bool
    {
        return $this->evaluate($this->isLabelLocalized);
    }

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label) ?? (string) Str::of($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
        return ($this->isLabelLocalized())?__($label):$label;
    }
}
