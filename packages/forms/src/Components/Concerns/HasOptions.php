<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

trait HasOptions
{
    /**
     * @var array<string | array<string>> | Arrayable | string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $options = null;

    /**
     * @param  array<string | array<string>> | Arrayable | string | Closure | null  $options
     */
    public function options(array | Arrayable | string | Closure | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options) ?? [];

        $enum = $options;
        if (
            is_string($enum) &&
            enum_exists($enum)
        ) {
            return collect($enum::cases())
                ->when(
                    is_a($enum, LabelInterface::class, allow_string: true),
                    fn (Collection $options): Collection => $options
                        ->mapWithKeys(fn ($case) => [
                            ($case?->value ?? $case->name) => $case->getLabel() ?? $case->name,
                        ]),
                    fn (Collection $options): Collection => $options
                        ->mapWithKeys(fn ($case) => [
                            ($case?->value ?? $case->name) => $case->name,
                        ]),
                )
                ->all();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function hasDynamicOptions(): bool
    {
        return $this->options instanceof Closure;
    }
}
