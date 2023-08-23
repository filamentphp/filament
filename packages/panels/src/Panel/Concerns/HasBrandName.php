<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBrandName
{
    protected string | Closure | null $brandName = null;

    public function brandName(string | Closure | null $name): static
    {
        $this->brandName = $name;

        return $this;
    }

    public function getBrandName(): string
    {
        return $this->evaluate($this->brandName) ?? config('app.name');
    }
}
