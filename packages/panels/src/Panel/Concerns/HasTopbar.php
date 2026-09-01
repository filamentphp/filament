<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Livewire\Topbar;
use Livewire\Component;

trait HasTopbar
{
    protected bool | Closure $hasTopbar = true;

    protected string | Closure | null $topbarLivewireComponent = null;

    public function topbar(bool | Closure $condition = true): static
    {
        $this->hasTopbar = $condition;

        return $this;
    }

    /**
     * @param  class-string<Component> | Closure | null  $component
     */
    public function topbarLivewireComponent(string | Closure | null $component): static
    {
        $this->topbarLivewireComponent = $component;

        return $this;
    }

    public function hasTopbar(): bool
    {
        return (bool) $this->evaluate($this->hasTopbar);
    }

    /**
     * @return class-string<Component>
     */
    public function getTopbarLivewireComponent(): string
    {
        return $this->evaluate($this->topbarLivewireComponent) ?? Topbar::class;
    }
}
