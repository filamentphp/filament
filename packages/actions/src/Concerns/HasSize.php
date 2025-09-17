<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Enums\Size;

trait HasSize
{
    protected Size | string | Closure | null $defaultSize = null;

    protected Size | string | Closure | null $size = null;

    public function defaultSize(Size | string | Closure | null $size): static
    {
        $this->defaultSize = $size;

        return $this;
    }

    public function size(Size | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getDefaultSize(): Size | string | null
    {
        return $this->evaluate($this->defaultSize);
    }

    public function getSize(): Size | string | null
    {
        return $this->evaluate($this->size) ?? $this->getDefaultSize();
    }
}
