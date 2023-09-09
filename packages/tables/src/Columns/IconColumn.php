<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Illuminate\Contracts\Support\Arrayable;

class IconColumn extends Column
{
    use Concerns\HasColor {
        getColor as getBaseColor;
    }
    use Concerns\HasIcon {
        getIcon as getBaseIcon;
    }

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.icon-column';

    protected bool | Closure $isBoolean = false;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $falseColor = null;

    protected string | Closure | null $falseIcon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $trueColor = null;

    protected string | Closure | null $trueIcon = null;

    protected bool | Closure $isListWithLineBreaks = false;

    protected IconColumnSize | string | Closure | null $size = null;

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function false(string | Closure | null $icon = null, string | array | Closure | null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function falseColor(string | array | Closure | null $color): static
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

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function true(string | Closure | null $icon = null, string | array | Closure | null $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function trueColor(string | array | Closure | null $color): static
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

    /**
     * @deprecated Use `icons()` instead.
     *
     * @param  array<mixed> | Arrayable | Closure  $options
     */
    public function options(array | Arrayable | Closure $options): static
    {
        $this->icons($options);

        return $this;
    }

    public function size(IconColumnSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): IconColumnSize | string | null
    {
        return $this->evaluate($this->size, [
            'state' => $state,
        ]);
    }

    public function getIcon(mixed $state): ?string
    {
        if (filled($icon = $this->getBaseIcon($state))) {
            return $icon;
        }

        if (! $this->isBoolean()) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueIcon() : $this->getFalseIcon();
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor(mixed $state): string | array | null
    {
        if (filled($color = $this->getBaseColor($state))) {
            return $color;
        }

        if (! $this->isBoolean()) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueColor() : $this->getFalseColor();
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getFalseColor(): string | array
    {
        return $this->evaluate($this->falseColor) ?? 'danger';
    }

    public function getFalseIcon(): string
    {
        return $this->evaluate($this->falseIcon) ?? 'heroicon-o-x-circle';
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getTrueColor(): string | array
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

    public function isListWithLineBreaks(): bool
    {
        return (bool) $this->evaluate($this->isListWithLineBreaks);
    }
}
