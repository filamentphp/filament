<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\HorizontalAlignment;

trait HasHorizontalAlignment
{
    protected HorizontalAlignment | string | Closure | null $horizontalAlignment = null;

    public function horizontalAlignment(HorizontalAlignment | string | Closure | null $alignment): static
    {
        $this->horizontalAlignment = $alignment;

        return $this;
    }

    public function horizontallyAlignStart(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Start : null);
    }

    public function horizontallyAlignCenter(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Center : null);
    }

    public function horizontallyAlignEnd(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::End : null);
    }

    public function horizontallyAlignStretch(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Stretch : null);
    }

    public function horizontallyAlignBaseline(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Baseline : null);
    }

    public function horizontallyAlignSpaceBetween(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Between : null);
    }

    public function horizontallyAlignSpaceAround(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Around : null);
    }

    public function horizontallyAlignSpaceEvenly(bool | Closure $condition = true): static
    {
        return $this->horizontalAlignment(fn (): ?HorizontalAlignment => $this->evaluate($condition) ? HorizontalAlignment::Evenly : null);
    }

    public function getHorizontalAlignment(): HorizontalAlignment | string | null
    {
        return $this->evaluate($this->horizontalAlignment);
    }
}
