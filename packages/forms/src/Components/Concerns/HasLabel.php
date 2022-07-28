<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasLabel
{
    protected bool | Closure $isLabelHidden = false;
    protected bool | Closure $isLabelLocalized = false;
    protected string | Htmlable | Closure | null $label = null;

    public function disableLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function localizeLabel(bool | Closure $condition = true): static
    {
        $this->isLabelLocalized = $condition;

        return $this;
    }

    public function label(string | Htmlable | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->isLabelLocalized()){
            return __($this->evaluate($this->label));
        }
        return $this->evaluate($this->label);
    }

    public function isLabelLocalized(): bool
    {
        return $this->evaluate($this->isLabelLocalized);
    }
    public function isLabelHidden(): bool
    {
        return $this->evaluate($this->isLabelHidden);
    }
}
