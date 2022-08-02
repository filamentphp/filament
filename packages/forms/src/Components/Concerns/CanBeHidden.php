<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Contracts\HasForms;
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

    public function hiddenOn(string | array $contexts): static
    {
        $this->hidden(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $hiddenContext) {
                if (in_array($hiddenContext, [$context, $livewire::class])) {
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

    public function whenTruthy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Closure $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function whenFalsy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Closure $get) use ($paths): bool {
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

    public function visibleOn(string | array $contexts): static
    {
        $this->visible(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $visibleContext) {
                if (in_array($visibleContext, [$context, $livewire::class])) {
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
