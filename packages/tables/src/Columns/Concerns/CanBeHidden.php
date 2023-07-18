<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected string | Closure | null $hiddenFrom = null;

    protected bool | Closure $isHidden = false;

    protected string | Closure | null $visibleFrom = null;

    protected bool | Closure $isVisible = true;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $livewireComponents
     */
    public function hiddenOn(string | array $livewireComponents): static
    {
        $this->hidden(static function (HasTable $livewire) use ($livewireComponents): bool {
            foreach (Arr::wrap($livewireComponents) as $livewireComponent) {
                if ($livewire instanceof $livewireComponent) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $livewireComponents
     */
    public function visibleOn(string | array $livewireComponents): static
    {
        $this->visible(static function (HasTable $livewire) use ($livewireComponents): bool {
            foreach (Arr::wrap($livewireComponents) as $livewireComponent) {
                if ($livewire instanceof $livewireComponent) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function hiddenFrom(string | Closure | null $breakpoint): static
    {
        $this->hiddenFrom = $breakpoint;

        return $this;
    }

    public function visibleFrom(string | Closure | null $breakpoint): static
    {
        $this->visibleFrom = $breakpoint;

        return $this;
    }

    public function getHiddenFrom(): ?string
    {
        return $this->evaluate($this->hiddenFrom);
    }

    public function getVisibleFrom(): ?string
    {
        return $this->evaluate($this->visibleFrom);
    }

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        return ! $this->evaluate($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
