<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;

class Radio extends Field
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasOptions;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.radio';

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

    protected bool | Closure $isButtonGroup = false;

    protected bool | Closure | null $isOptionDisabled = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function buttonGroup(bool | Closure $condition = true): static
    {
        $this->isButtonGroup = $condition;

        return $this;
    }

    public function isButtonGroup(): bool
    {
        return (bool) $this->evaluate($this->isButtonGroup);
    }

    public function boolean(string $trueLabel = 'Yes', string $falseLabel = 'No'): static
    {
        $this->options([
            1 => $trueLabel,
            0 => $falseLabel,
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

    /**
     * @param  array-key  $value
     */
    public function isOptionDisabled($value, string $label): bool
    {
        if ($this->isOptionDisabled === null) {
            return false;
        }

        return (bool) $this->evaluate($this->isOptionDisabled, [
            'label' => $label,
            'value' => $value,
        ]);
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
