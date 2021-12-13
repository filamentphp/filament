<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Component;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected $isHidden = false;

    public function hidden(bool | callable $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function when(bool | callable $condition = true): static
    {
        $this->visible($condition);

        return $this;
    }

    public function whenTruthy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(function (callable $get) use ($paths): bool {
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

        $this->hidden(function (callable $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! ! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function visible(bool | callable $condition = true): static
    {
        $this->isHidden = fn (): bool => ! $this->evaluate($condition);

        return $this;
    }

    public function isHidden(): bool
    {
        return (bool) $this->evaluate($this->isHidden);
    }
}
