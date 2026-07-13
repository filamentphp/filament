<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasDescription;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

trait HasDescriptions
{
    /**
     * @var array<string | Htmlable> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $descriptions = [];

    /**
     * @var ?array<string | Htmlable>
     */
    protected ?array $cachedDescriptions = null;

    protected bool $hasCachedDescriptions = false;

    /**
     * @param  array<string | Htmlable> | Arrayable | Closure  $descriptions
     */
    public function descriptions(array | Arrayable | Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

        $this->cachedDescriptions = null;
        $this->hasCachedDescriptions = false;

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
        if ($this->hasCachedDescriptions) {
            return $this->cachedDescriptions;
        }

        $descriptions = $this->evaluate($this->descriptions);

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        if (
            blank($descriptions) &&
            filled($enum = $this->getEnum()) &&
            is_a($enum, HasDescription::class, allow_string: true)
        ) {
            $descriptions = array_reduce($enum::cases(), function (array $carry, HasDescription & UnitEnum $case): array {
                if (filled($description = $case->getDescription())) {
                    $carry[$case->value ?? $case->name] = $description;
                }

                return $carry;
            }, []);
        }

        $this->hasCachedDescriptions = true;

        return $this->cachedDescriptions = $descriptions;
    }
}
