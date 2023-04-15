<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanFilter
{
    protected string | Closure | null $filterName = null;

    protected string | Closure | null $filterFunctionName = null;

    public function filterName(string | Closure | null $name): static
    {
        $this->filterName = $name;

        return $this;
    }

    public function getFilterName(): ?string
    {
        return $this->evaluate($this->filterName);
    }

    public function hasFilter(): bool
    {
        return filled($this->getFilterName()) || filled($this->getFilterFunctionName());
    }

    public function filterFunctionName(string | Closure | null $name): static
    {
        $this->filterFunctionName = $name;

        return $this;
    }

    public function getFilterFunctionName(): ?string
    {
        return $this->evaluate($this->filterFunctionName);
    }
}
