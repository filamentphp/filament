<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;

class Radio extends Field implements Contracts\CanDisableOptions
{
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasOptions;

    public const RADIOS_VIEW = 'filament-forms::components.radio.index';

    public const BUTTONS_VIEW = 'filament-forms::components.radio.buttons';

    public const BUTTON_GROUP_VIEW = 'filament-forms::components.radio.button-group';

    /**
     * @var view-string
     */
    protected string $view = self::RADIOS_VIEW;

    protected bool | Closure $isInline = false;

    /**
     * @var array<string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $colors = [];

    /**
     * @var array<string | Htmlable> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $descriptions = [];

    /**
     * @var array<string | Htmlable | null> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $icons = [];

    protected bool | Closure | null $isOptionDisabled = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function radios(): static
    {
        return $this->view(static::RADIOS_VIEW);
    }

    public function isRadios(): bool
    {
        return $this->getView() === static::RADIOS_VIEW;
    }

    public function buttons(): static
    {
        return $this->view(static::BUTTONS_VIEW);
    }

    public function isButtons(): bool
    {
        return $this->getView() === static::BUTTONS_VIEW;
    }

    public function buttonGroup(): static
    {
        return $this->view(static::BUTTON_GROUP_VIEW);
    }

    public function isButtonGroup(): bool
    {
        return $this->getView() === static::BUTTON_GROUP_VIEW;
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.radio.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.radio.boolean.false'),
        ]);

        return $this;
    }

    /**
     * @param  array<string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null> | Arrayable | Closure  $colors
     */
    public function colors(array | Arrayable | Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor($value): string | array | null
    {
        return $this->getColors()[$value] ?? null;
    }

    /**
     * @return array<string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null>
     */
    public function getColors(): array
    {
        $colors = $this->evaluate($this->colors);

        if ($colors instanceof Arrayable) {
            $colors = $colors->toArray();
        }

        return $colors;
    }

    public function disableOptionWhen(bool | Closure $callback): static
    {
        $this->isOptionDisabled = $callback;

        return $this;
    }

    /**
     * @param  array<string | Htmlable | null> | Arrayable | Closure  $icons
     */
    public function icons(array | Arrayable | Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function getIcon($value): string | Htmlable | null
    {
        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable | null>
     */
    public function getIcons(): array
    {
        $icons = $this->evaluate($this->icons);

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        return $icons;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;
        $this->inlineLabel(fn (Radio $component): ?bool => ($component->isRadios() && $component->evaluate($condition)) ? true : null);

        return $this;
    }

    /**
     * @param  array<string | Htmlable> | Arrayable | Closure  $descriptions
     */
    public function descriptions(array | Arrayable | Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasDescription($value): bool
    {
        return array_key_exists($value, $this->getDescriptions());
    }

    /**
     * @param  array-key  $value
     */
    public function getDescription($value): string | Htmlable | null
    {
        return $this->getDescriptions()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        $descriptions = $this->evaluate($this->descriptions);

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        return $descriptions;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }
}
