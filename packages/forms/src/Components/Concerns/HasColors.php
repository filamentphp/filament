<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasColor as ColorInterface;
use Illuminate\Contracts\Support\Arrayable;
use UnitEnum;

trait HasColors
{
    /**
     * @var array<string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $colors = null;

    /**
     * @param  array<string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null> | Arrayable | Closure | null  $colors
     */
    public function colors(array | Arrayable | Closure | null $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor(mixed $value): string | array | null
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

        if (
            is_string($this->options) &&
            enum_exists($enum = $this->options) &&
            is_a($enum, ColorInterface::class, allow_string: true)
        ) {
            return array_reduce($enum::cases(), function (array $carry, ColorInterface & UnitEnum $case): array {
                $carry[$case?->value ?? $case->name] = $case->getColor();

                return $carry;
            }, []);
        }

        return $colors ?? [];
    }
}
