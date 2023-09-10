<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Enums\ActionSize;

trait HasSize
{
    protected ActionSize | string | Closure | null $defaultSize = null;

    protected ActionSize | string | Closure | null $size = null;

    public function defaultSize(ActionSize | string | Closure | null $size): static
    {
        $this->defaultSize = $size;

        return $this;
    }

    public function size(ActionSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getDefaultSize(): ActionSize | string | null
    {
        return $this->evaluate($this->defaultSize);
    }

    public function getSize(): ActionSize | string | null
    {
        return $this->evaluate($this->size) ?? $this->getDefaultSize();
    }
}
