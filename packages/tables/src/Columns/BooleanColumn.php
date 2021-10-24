<?php

namespace Filament\Tables\Columns;

class BooleanColumn extends Column
{
    protected string $view = 'tables::columns.boolean-column';

    protected ?string $falseColor = null;

    protected ?string $falseIcon = null;

    protected ?string $trueColor = null;

    protected ?string $trueIcon = null;

    public function false(?string $icon = null, ?string $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    public function falseColor(?string $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    public function falseIcon(?string $icon): static
    {
        $this->falseIcon = $icon;

        return $this;
    }

    public function true(?string $icon = null, ?string $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    public function trueColor(?string $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function trueIcon(?string $icon): static
    {
        $this->trueIcon = $icon;

        return $this;
    }

    public function getFalseColor(): ?string
    {
        return $this->falseColor;
    }

    public function getFalseIcon(): ?string
    {
        return $this->falseIcon;
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
        return $this->trueColor;
    }

    public function getTrueIcon(): ?string
    {
        return $this->trueIcon;
    }
}
