<?php

namespace Filament\Actions\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasGroupedIcon
{
    protected string | BackedEnum | Htmlable | Closure | null $groupedIcon = null;

    public function groupedIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->groupedIcon = $icon;

        return $this;
    }

    public function getGroupedIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->groupedIcon);
    }
}
