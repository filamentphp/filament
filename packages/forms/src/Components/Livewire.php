<?php

namespace Filament\Forms\Components;

use Closure;

class Livewire extends Component
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.livewire';

    protected bool | Closure $isLazy = false;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $data = [];

    protected string | Closure $component;

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    final public function __construct(string | Closure $component, array | Closure $data = [])
    {
        $this->component($component);
        $this->data($data);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public static function make(string | Closure $component, array | Closure $data = []): static
    {
        $static = app(static::class, [
            'component' => $component,
            'data' => $data,
        ]);
        $static->configure();

        return $static;
    }

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
