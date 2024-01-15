<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use UnitEnum;

trait HasOptions
{
    /**
     * @var array<string | array<string>> | Arrayable | class-string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $options = null;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    /**
     * @param  array<string | array<string>> | Arrayable | class-string | Closure | null  $options
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
        $options = $this->evaluate($this->options);

        if (
            is_string($options) &&
            enum_exists($enum = $options)
        ) {
            if (is_a($enum, LabelInterface::class, allow_string: true)) {
                return array_reduce($enum::cases(), function (array $carry, LabelInterface & UnitEnum $case): array {
                    $carry[$case?->value ?? $case->name] = $case->getLabel() ?? $case->name;

                    return $carry;
                }, []);
            }

            return array_reduce($enum::cases(), function (array $carry, UnitEnum $case): array {
                $carry[$case?->value ?? $case->name] = $case->name;

                return $carry;
            }, []);
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options ?? [];
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function getOptionLabelUsingCallback(): ?Closure
    {
        return $this->getOptionLabelUsing;
    }

    public function getOptionLabelsUsingCallback(): ?Closure
    {
        return $this->getOptionLabelsUsing;
    }

    public function getSearchResultsUsingCallback(): ?Closure
    {
        return $this->getSearchResultsUsing;
    }
}
