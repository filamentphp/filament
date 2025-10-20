<?php

namespace Filament\Forms\Components;

use Closure;

class LivewireField extends Field
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.livewire-field';

    protected bool | Closure $isLazy = false;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $data = [];

    protected string | Closure $component;

    public function component(string | Closure $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): string
    {
        return $this->evaluate($this->component);
    }

    public function lazy(bool | Closure $condition = true): static
    {
        $this->isLazy = $condition;

        return $this;
    }

    public function isLazy(): bool
    {
        return (bool) $this->evaluate($this->isLazy);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function data(array | Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->evaluate($this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function getComponentProperties(): array
    {
        $properties = [
            'record' => $this->getRecord(),
            $this->applyStateBindingModifiers('wire:model') => $this->getStatePath(),
        ];

        if ($this->isLazy()) {
            $properties['lazy'] = true;
        }

        return [
            ...$properties,
            ...$this->getData(),
        ];
    }
}
