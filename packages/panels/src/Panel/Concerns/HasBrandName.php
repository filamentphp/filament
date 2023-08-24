<?php

namespace Filament\Panel\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasBrandName
{
    protected string | Closure | Htmlable | null $brandName = null;

    public function brandName(string | Closure | Htmlable | null $name): static
    {
        $this->brandName = $name;

        return $this;
    }

    public function getBrandName(): string
    {
        return $this->evaluate($this->brandName) ?? config('app.name');
    }
}
