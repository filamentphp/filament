<?php

namespace Filament\Tables\Columns;

use Closure;

class BooleanColumn extends Column
{
    protected string $view = 'tables::columns.boolean-column';

    protected string | Closure | null $falseColor = null;

    protected string | Closure | null $falseIcon = null;

    protected string | Closure | null $trueColor = null;

    protected string | Closure | null $trueIcon = null;

    public function false(string | Closure | null $icon = null, string | Closure | null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    public function falseColor(string | Closure | null $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    public function falseIcon(string | Closure | null $icon): static
    {
        $this->falseIcon = $icon;

        return $this;
    }

    public function true(string | Closure | null $icon = null, string | Closure | null $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    public function trueColor(string | Closure | null $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function trueIcon(string | Closure | null $icon): static
    {
        $this->trueIcon = $icon;

        return $this;
    }

    public function getFalseColor(): ?string
    {
        return $this->evaluate($this->falseColor);
    }

    public function getFalseIcon(): ?string
    {
        return $this->evaluate($this->falseIcon);
    }

    public function getStateColor(): ?string
    {
        $state = $this->getState();

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueColor() : $this->getFalseColor();
    }

    public function getStateIcon(): ?string
    {
        $state = $this->getState();

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueIcon() : $this->getFalseIcon();
    }

    public function getTrueColor(): ?string
    {
        return $this->evaluate($this->trueColor);
    }

    public function getTrueIcon(): ?string
    {
        return $this->evaluate($this->trueIcon);
    }
}
