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

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.radio';

    protected bool | Closure $isInline = false;

    /**
     * @var array<string | Htmlable> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $descriptions = [];

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.radio.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.radio.boolean.false'),
        ]);

        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;
        $this->inlineLabel(fn (Radio $component): ?bool => $component->evaluate($condition) ? true : null);

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
