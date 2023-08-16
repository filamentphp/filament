<?php

namespace Filament\Forms\Components;

use Closure;

class Livewire extends Component
{
    protected bool|Closure $isLazy = false;
    protected array|Closure $componentData = [];
    protected bool|Closure $isWithRecord = false;

    /**
     * @param string $component
     */
    final public function __construct(string $component)
    {
        $this->view('filament-forms::components.livewire', compact('component'));
    }

    /**
     * @param string $component
     * @return Livewire
     */
    public static function make(string $component): static
    {
        $static = app(static::class, compact('component'));
        $static->configure();

        return $static;
    }

    public function lazy(bool|Closure $condition = true): static
    {
        $this->isLazy = $condition;

        return $this;
    }

    public function isLazy(): bool
    {
        return (bool)$this->evaluate($this->isLazy);
    }

    public function componentData(array|Closure $data = []): static
    {
        $this->componentData = $data;

        return $this;
    }

    public function getComponentData(): array
    {
        return (array)$this->evaluate($this->componentData);
    }

    public function withRecord(bool|Closure $condition = true): static
    {
        $this->isWithRecord = $condition;

        return $this;
    }

    public function isWithRecord(): bool
    {
        return (bool)$this->evaluate($this->isWithRecord);
    }
}
