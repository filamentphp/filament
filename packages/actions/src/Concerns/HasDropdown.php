<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasDropdown
{
    protected bool | Closure $hasDropdown = true;

    protected string | Closure | null $dropdownPlacement = null;

    protected string | Closure | null $dropdownMaxHeight = null;

    protected string | Closure | null $dropdownWidth = null;

    public function dropdown(bool | Closure $condition = true): static
    {
        $this->hasDropdown = $condition;

        return $this;
    }

    public function dropdownPlacement(string | Closure | null $placement): static
    {
        $this->dropdownPlacement = $placement;

        return $this;
    }

    public function dropdownMaxHeight(string | Closure | null $height): static
    {
        $this->dropdownMaxHeight = $height;

        return $this;
    }

    public function dropdownWidth(string | Closure | null $width): static
    {
        $this->dropdownWidth = $width;

        return $this;
    }

    public function getDropdownPlacement(): ?string
    {
        return $this->evaluate($this->dropdownPlacement);
    }

    public function getDropdownMaxHeight(): ?string
    {
        return $this->evaluate($this->dropdownMaxHeight);
    }

    public function getDropdownWidth(): ?string
    {
        return $this->evaluate($this->dropdownWidth);
    }

    public function hasDropdown(): bool
    {
        return (bool) $this->evaluate($this->hasDropdown);
    }
}
