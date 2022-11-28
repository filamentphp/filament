<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
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
     * @param string | array<string> $contexts
     */
    public function hiddenOn(string | array $contexts): static
    {
        $this->hidden(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $hiddenContext) {
                if ($hiddenContext === $context || $livewire instanceof $hiddenContext) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function when(bool | Closure $condition = true): static
    {
        $this->visible($condition);

        return $this;
    }

    /**
     * @param string | array<string> $paths
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
     * @param string | array<string> $paths
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
     * @param string | array<string> $contexts
     */
    public function visibleOn(string | array $contexts): static
    {
        $this->visible(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $visibleContext) {
                if ($visibleContext === $context || $livewire instanceof $visibleContext) {
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
}
