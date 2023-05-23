<?php

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class IconColumn extends Column
{
    use Concerns\HasColors {
        getStateColor as getBaseStateColor;
    }
    use Concerns\HasSize;

    protected string $view = 'tables::columns.icon-column';

    protected array | Arrayable | Closure $options = [];

    protected bool | Closure $isBoolean = false;

    protected string | Closure | null $falseColor = null;

    protected string | Closure | null $falseIcon = null;

    protected string | Closure | null $trueColor = null;

    protected string | Closure | null $trueIcon = null;

    public function options(array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function false(string | Closure | null $icon = null, string | Closure | null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    public function falseColor(string | Closure | null $color): static
    {
        $this->boolean();
        $this->falseColor = $color;

        return $this;
    }

    public function falseIcon(string | Closure | null $icon): static
    {
        $this->boolean();
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
        $this->boolean();
        $this->trueColor = $color;

        return $this;
    }

    public function trueIcon(string | Closure | null $icon): static
    {
        $this->boolean();
        $this->trueIcon = $icon;

        return $this;
    }

    public function getStateIcon(): ?string
    {
        $state = $this->getState();
        $record = $this->getRecord();

        if ($this->isBoolean()) {
            if ($state === null) {
                return null;
            }

            return $state ? $this->getTrueIcon() : $this->getFalseIcon();
        }

        $stateIcon = null;

        foreach ($this->getOptions() as $icon => $condition) {
            if (is_numeric($icon)) {
                $stateIcon = $condition;
            } elseif ($condition instanceof Closure && $condition($state, $record)) {
                $stateIcon = $icon;
            } elseif ($condition === $state) {
                $stateIcon = $icon;
            }
        }

        return $stateIcon;
    }

    public function getStateColor(): ?string
    {
        if ($this->isBoolean()) {
            $state = $this->getState();

            if ($state === null) {
                return null;
            }

            return $state ? $this->getTrueColor() : $this->getFalseColor();
        }

        return $this->getBaseStateColor();
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getFalseColor(): string
    {
        return $this->evaluate($this->falseColor) ?? 'danger';
    }

    public function getFalseIcon(): string
    {
        return $this->evaluate($this->falseIcon) ?? 'heroicon-o-x-circle';
    }

    public function getTrueColor(): string
    {
        return $this->evaluate($this->trueColor) ?? 'success';
    }

    public function getTrueIcon(): string
    {
        return $this->evaluate($this->trueIcon) ?? 'heroicon-o-check-circle';
    }

    public function isBoolean(): bool
    {
        return (bool) $this->evaluate($this->isBoolean);
    }
}
