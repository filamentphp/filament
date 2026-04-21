<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;

trait HasLabel
{
    protected bool | Closure $isLabelHidden = false;

    protected string | Closure | null $label = null;

    protected bool $shouldTranslateLabel = false;

    public function hiddenLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function translateLabel(bool $shouldTranslateLabel = true): static
    {
        $this->shouldTranslateLabel = $shouldTranslateLabel;

        return $this;
    }

    public function getLabel(): ?string
    {
        $label = $this->evaluate($this->label);

        if ($label === null) {
            return $this->getDefaultLabel();
        }

        if (blank($label)) {
            return null;
        }

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    public function isLabelHidden(): bool
    {
        return (bool) $this->evaluate($this->isLabelHidden);
    }

    public function getDefaultLabel(): ?string
    {
        return null;
    }
}
