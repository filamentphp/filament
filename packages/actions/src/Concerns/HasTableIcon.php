<?php

namespace Filament\Actions\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasTableIcon
{
    protected string | BackedEnum | Htmlable | Closure | null $tableIcon = null;

    public function tableIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->tableIcon = $icon;

        return $this;
    }

    public function getTableIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->tableIcon);
    }
}
