<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $operations
     */
    public function hiddenOn(string | array $operations): static
    {
        $this->hidden(static function (HasForms $livewire, string $operation) use ($operations): bool {
            foreach (Arr::wrap($operations) as $hiddenOperation) {
                if ($hiddenOperation === $operation || $livewire instanceof $hiddenOperation) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function hiddenWhenAllChildComponentsHidden(): static
    {
        $this->hidden(static function (Component $component): bool {
            foreach ($component->getChildComponentContainers() as $childComponentContainer) {
                foreach ($childComponentContainer->getComponents(withHidden: false) as $childComponent) {
                    return false;
                }
            }

            return true;
        });

        return $this;
    }

    /**
     * @param  string | array<string>  $paths
     */
    public function whenTruthy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Get $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    /**
     * @param  string | array<string>  $paths
     */
    public function whenFalsy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Get $get) use ($paths): bool {
            foreach ($paths as $path) {
                if ((bool) $get($path)) {
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
     * @param  string | array<string>  $operations
     */
    public function visibleOn(string | array $operations): static
    {
        $this->visible(static function (string $operation, HasForms $livewire) use ($operations): bool {
            foreach (Arr::wrap($operations) as $visibleOperation) {
                if ($visibleOperation === $operation || $livewire instanceof $visibleOperation) {
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

        return ! $this->evaluate($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
