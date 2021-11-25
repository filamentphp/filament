<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Support\Arrayable;

class Radio extends Field
{
    protected string $view = 'forms::components.radio';

    protected $options = [];

    protected $descriptions = [];

    protected $isOptionDisabled = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getOptionLabelUsing(function (Radio $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });
    }

    public function boolean(string $trueLabel = 'Yes', string $falseLabel = 'No'): static
    {
        $this->options([
            1 => $trueLabel,
            0 => $falseLabel,
        ]);

        return $this;
    }

    public function disableOptionWhen(bool | callable $callback): static
    {
        $this->isOptionDisabled = $callback;

        return $this;
    }

    public function getOptionLabelUsing(callable $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function options(array | Arrayable | callable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptionLabel(): ?string
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => $this->getState(),
        ]);
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function hasDescription($value): bool
    {
        return isset($this->getDescriptions[$value]);
    }

    public function getDescription($value): string
    {
        if ($this->hasDescription($value)) {
            return $this->getDescriptions[$value];
        }

        return '';
    }

    public function getDescriptions(): array
    {
        $descriptions = $this->evaluate($this->descriptions);

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        return $descriptions;
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
