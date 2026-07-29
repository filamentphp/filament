<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasIcon as IconInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

trait HasIcons
{
    /**
     * @var array<string| BackedEnum | Htmlable | null> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $icons = null;

    /**
     * @var array<string | BackedEnum | Htmlable | null> | null
     */
    protected ?array $cachedIcons = null;

    protected bool $hasCachedIcons = false;

    /**
     * @param  array<string | BackedEnum | Htmlable | null> | Arrayable | Closure | null  $icons
     */
    public function icons(array | Arrayable | Closure | null $icons): static
    {
        $this->icons = $icons;

        $this->cachedIcons = null;
        $this->hasCachedIcons = false;

        return $this;
    }

    public function getIcon(mixed $value): string | BackedEnum | Htmlable | null
    {
        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | BackedEnum | Htmlable | null>
     */
    public function getIcons(): array
    {
        if ($this->hasCachedIcons) {
            return $this->cachedIcons;
        }

        $icons = $this->evaluate($this->icons);

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        if (
            blank($icons) &&
            filled($enum = $this->getEnum()) &&
            is_a($enum, IconInterface::class, allow_string: true)
        ) {
            $icons = array_reduce($enum::cases(), function (array $carry, IconInterface & UnitEnum $case): array {
                $carry[$case->value ?? $case->name] = $case->getIcon();

                return $carry;
            }, []);
        }

        $this->hasCachedIcons = true;

        return $this->cachedIcons = $icons ?? [];
    }
}
