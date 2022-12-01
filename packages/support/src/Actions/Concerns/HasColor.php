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
        $this->color('danger');
        
        return $this;
    }

    public function colorPrimary(): static
    {
        $this->color('primary');
        
        return $this;
    }

    public function colorSecondary(): static
    {
        $this->color('secondary');
        
        return $this;
    }

    public function colorSuccess(): static
    {
        $this->color('success');
        
        return $this;
    }

    public function colorWarning(): static
    {
        $this->color('warning');
        
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->color);
    }
}
