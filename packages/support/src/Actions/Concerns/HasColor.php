<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasColor
{
    protected string | Closure | null $color = null;

    public function color(string | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function colorDanger(): static
    {
        return $this->color('danger');
    }

    public function colorPrimary(): static
    {
        return $this->color('primary');
    }

    public function colorSecondary(): static
    {
        return $this->color('secondary');
    }

    public function colorSuccess(): static
    {
        return $this->color('success');
    }

    public function colorWarning(): static
    {
        return $this->color('warning');
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->color);
    }
}
