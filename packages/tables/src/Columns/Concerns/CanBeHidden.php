<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    use CanBeHiddenResponsively;

    protected bool | Closure $isHidden = false;

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

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return true;
        }

        return $this->getLayout()?->getCloneForRecord($this->getRecord(), $this->getRowLoop())->isHidden() ?? false;
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
