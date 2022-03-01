<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class Radio extends Field
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasOptions;

    protected string $view = 'forms::components.radio';

    protected bool | Closure $isInline = false;

    protected array | Arrayable | Closure $descriptions = [];

    protected bool | Closure | null $isOptionDisabled = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function boolean(string $trueLabel = 'Yes', string $falseLabel = 'No'): static
    {
        $this->options([
            1 => $trueLabel,
            0 => $falseLabel,
        ]);

        return $this;
    }

    public function disableOptionWhen(bool | Closure $callback): static
    {
        $this->isOptionDisabled = $callback;

        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function descriptions(array | Arrayable | Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    public function hasDescription($value): bool
    {
        return array_key_exists($value, $this->getDescriptions());
    }

    public function getDescription($value): ?string
    {
        return $this->getDescriptions()[$value] ?? null;
    }

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
}
