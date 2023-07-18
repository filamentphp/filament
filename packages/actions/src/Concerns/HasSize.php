<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasSize
{
    protected string | Closure | null $defaultSize = null;

    protected string | Closure | null $size = null;

    public function defaultSize(string | Closure | null $size): static
    {
        $this->defaultSize = $size;

        return $this;
    }

    public function size(string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getDefaultSize(): ?string
    {
        return $this->evaluate($this->defaultSize);
    }

    public function getSize(): ?string
    {
        return $this->evaluate($this->size) ?? $this->getDefaultSize();
    }
}
