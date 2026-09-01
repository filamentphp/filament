<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasColor as ColorInterface;
use Illuminate\Contracts\Support\Arrayable;
use UnitEnum;

trait HasColors
{
    /**
     * @var array<string | array<int | string, string | int> | null> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $colors = null;

    /**
     * @var array<string | array<int | string, string | int> | null> | null
     */
    protected ?array $cachedColors = null;

    protected bool $hasCachedColors = false;

    /**
     * @param  array<string | array<int | string, string | int> | null> | Arrayable | Closure | null  $colors
     */
    public function colors(array | Arrayable | Closure | null $colors): static
    {
        $this->colors = $colors;

        $this->cachedColors = null;
        $this->hasCachedColors = false;

        return $this;
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getColor(mixed $value): string | array | null
    {
        return $this->getColors()[$value] ?? null;
    }

    /**
     * @return array<string | array<int | string, string | int> | null>
     */
    public function getColors(): array
    {
        if ($this->hasCachedColors) {
            return $this->cachedColors;
        }

        $colors = $this->evaluate($this->colors);

        if ($colors instanceof Arrayable) {
            $colors = $colors->toArray();
        }

        if (
            blank($colors) &&
            filled($enum = $this->getEnum()) &&
            is_a($enum, ColorInterface::class, allow_string: true)
        ) {
            $colors = array_reduce($enum::cases(), function (array $carry, ColorInterface & UnitEnum $case): array {
                $carry[$case->value ?? $case->name] = $case->getColor();

                return $carry;
            }, []);
        }

        $this->hasCachedColors = true;

        return $this->cachedColors = $colors ?? [];
    }
}
