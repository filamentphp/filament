<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasIcon as IconInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

trait HasIcons
{
    /**
     * @var array<string | Htmlable | null> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $icons = null;

    /**
     * @param  array<string | Htmlable | null> | Arrayable | Closure | null  $icons
     */
    public function icons(array | Arrayable | Closure | null $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function getIcon(mixed $value): string | Htmlable | null
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

        if (
            is_string($this->options) &&
            enum_exists($enum = $this->options) &&
            is_a($enum, IconInterface::class, allow_string: true)
        ) {
            return array_reduce($enum::cases(), function (array $carry, IconInterface & UnitEnum $case): array {
                $carry[$case?->value ?? $case->name] = $case->getIcon();

                return $carry;
            }, []);
        }

        return $icons ?? [];
    }
}
