<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Enums\MaxWidth;

trait HasDropdown
{
    protected bool | Closure $hasDropdown = true;

    protected string | Closure | null $dropdownPlacement = null;

    protected string | Closure | null $dropdownMaxHeight = null;

    protected MaxWidth | string | Closure | null $dropdownWidth = null;

    protected int | Closure $dropdownOffset = 8;

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

    public function dropdownWidth(MaxWidth | string | Closure | null $width): static
    {
        $this->dropdownWidth = $width;

        return $this;
    }

    public function dropdownOffset(int | Closure $offset): static
    {
        $this->dropdownOffset = $offset;

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

    public function getDropdownWidth(): MaxWidth | string | null
    {
        return $this->evaluate($this->dropdownWidth);
    }

    public function getDropdownOffset(): int
    {
        return $this->evaluate($this->dropdownOffset);
    }

    public function hasDropdown(): bool
    {
        return (bool) $this->evaluate($this->hasDropdown);
    }
}
