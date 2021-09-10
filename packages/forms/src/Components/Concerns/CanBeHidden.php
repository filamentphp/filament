<?php

namespace Filament\Forms\Components\Concerns;

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

    public function whenTruthy(string $path): static
    {
        $this->hidden(fn (callable $get): bool => ! $get($path));

        return $this;
    }

    public function whenFalsy(string $path): static
    {
        $this->hidden(fn (callable $get): bool => (bool) $get($path));

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
