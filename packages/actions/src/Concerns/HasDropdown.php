<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasDropdown
{
    protected string | Closure | null $dropdownPlacement = null;

    public function dropdownPlacement(string | Closure | null $placement): static
    {
        $this->dropdownPlacement = $placement;

        return $this;
    }

    public function getDropdownPlacement(): ?string
    {
        return $this->evaluate($this->dropdownPlacement);
    }
}
