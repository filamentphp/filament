<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class CheckboxList extends Field
{
    protected string $view = 'forms::components.checkbox-list';

    protected array | Closure $options = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);
    }

    public function options(array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }
}
