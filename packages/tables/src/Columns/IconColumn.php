<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Columns\IconColumn\Enums\IconColumnSize;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;
use function Filament\Support\get_color_css_variables;

class IconColumn extends Column implements HasEmbeddedView
{
    use Concerns\CanWrap;
    use Concerns\HasColor {
        getColor as getBaseColor;
    }
    use Concerns\HasIcon {
        getIcon as getBaseIcon;
    }

    protected bool | Closure | null $isBoolean = null;

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

    public function getSize(mixed $state): IconColumnSize | string
    {
        $size = $this->evaluate($this->size, [
            'state' => $state,
        ]);

        if (blank($size)) {
            return IconColumnSize::Large;
        }

        if (is_string($size)) {
            $size = IconColumnSize::tryFrom($size) ?? $size;
        }

        if ($size === 'base') {
            return IconColumnSize::Large;
        }

        return $size;
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
        return $this->evaluate($this->falseIcon)
            ?? FilamentIcon::resolve('tables::columns.icon-column.false')
            ?? 'heroicon-o-x-circle';
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
        return $this->evaluate($this->trueIcon)
            ?? FilamentIcon::resolve('tables::columns.icon-column.true')
            ?? 'heroicon-o-check-circle';
    }

    public function isBoolean(): bool
    {
        if (blank($this->isBoolean)) {
            $this->isBoolean = $this->getRecord()?->hasCast($this->getName(), ['bool', 'boolean']);
        }

        return (bool) $this->evaluate($this->isBoolean);
    }

    public function isListWithLineBreaks(): bool
    {
        return (bool) $this->evaluate($this->isListWithLineBreaks);
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-icon',
                'fi-inline' => $this->isInline(),
            ]);

        if (empty($state)) {
            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder !== null)) { ?>
                    <p class="fi-ta-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $alignment = $this->getAlignment();

        $attributes = $attributes
            ->class([
                'fi-ta-icon-has-line-breaks' => $this->isListWithLineBreaks(),
                'fi-wrapped' => $this->canWrap(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        ob_start(); ?>
        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?php
                    $color = $this->getColor($stateItem);
                ?>

                <?= generate_icon_html($this->getIcon($stateItem), attributes: (new ComponentAttributeBag)
                    ->class([
                        match ($color) {
                            null, 'gray' => null,
                            default => 'fi-color-custom',
                        } => filled($color),
                        is_string($color) ? "fi-color-{$color}" : null,
                        (($size = $this->getSize($stateItem)) instanceof IconColumnSize) ? "fi-size-{$size->value}" : $size,
                    ])
                    ->style([
                        get_color_css_variables(
                            $color,
                            shades: [400, 500],
                            alias: 'tables::columns.icon-column.item',
                        ) => $color !== 'gray',
                    ]))
                    ->toHtml() ?>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }
}
