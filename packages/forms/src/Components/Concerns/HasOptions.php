<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;

trait HasOptions
{
    /**
     * @var array<string | array<string>> | Arrayable | string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $prependedOptions = null;

    /**
     * @var array<string | array<string>> | Arrayable | string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $options = null;

    /**
     * @param  array<string | array<string>> | Arrayable | string | Closure | null  $options
     */
    public function prependOptions(array | Arrayable | string | Closure | null $options): static
    {
        $this->prependedOptions = $options;

        return $this;
    }

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
        return $this->getOptionsHelper($this->prependedOptions) + $this->getOptionsHelper($this->options);
    }

    /**
     * @param  array<string | array<string>> | Arrayable | string | Closure | null  $opt
     *
     * @return array<string | array<string>>
     */
    private function getOptionsHelper(array | Arrayable | string | Closure | null $opt): array
    {
        $options = $this->evaluate($opt) ?? [];

        $enum = $options;
        if (
            is_string($enum) &&
            enum_exists($enum)
        ) {
            if (is_a($enum, LabelInterface::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getLabel() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
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
