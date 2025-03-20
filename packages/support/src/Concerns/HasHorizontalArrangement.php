<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\HorizontalArrangement;

trait HasHorizontalArrangement
{
    protected HorizontalArrangement | string | Closure | null $horizontalArrangement = null;

    public function horizontalArrangement(HorizontalArrangement | string | Closure | null $alignment): static
    {
        $this->horizontalArrangement = $alignment;

        return $this;
    }

    public function horizontallyArrangeStart(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Start : null);
    }

    public function horizontallyArrangeCenter(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Center : null);
    }

    public function horizontallyArrangeEnd(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::End : null);
    }

    public function horizontallyArrangeStretch(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Stretch : null);
    }

    public function horizontallyArrangeBaseline(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Baseline : null);
    }

    public function horizontallyArrangeSpaceBetween(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Between : null);
    }

    public function horizontallyArrangeSpaceAround(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Around : null);
    }

    public function horizontallyArrangeSpaceEvenly(bool | Closure $condition = true): static
    {
        return $this->horizontalArrangement(fn (): ?HorizontalArrangement => $this->evaluate($condition) ? HorizontalArrangement::Evenly : null);
    }

    public function getHorizontalArrangement(): HorizontalArrangement | string | null
    {
        return $this->evaluate($this->horizontalArrangement);
    }
}
