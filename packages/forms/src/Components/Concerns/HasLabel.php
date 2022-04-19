<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasLabel
{
    protected bool | Closure $isLabelHidden = false;

    protected string | HtmlString | Closure | null $label = null;

    public function disableLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | HtmlString | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string | HtmlString | null
    {
        return $this->evaluate($this->label);
    }

    public function isLabelHidden(): bool
    {
        return $this->evaluate($this->isLabelHidden);
    }
}
