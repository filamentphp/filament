<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

trait HasLabel
{
    protected string | Htmlable | Closure | null $label = null;

    protected bool | Closure $isLabelHidden = false;

    public function disableLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | Htmlable | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->label)  ?? (string) Str::of($this->getName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
    }

    public function isLabelHidden(): bool
    {
        return $this->evaluate($this->isLabelHidden);
    }
}
