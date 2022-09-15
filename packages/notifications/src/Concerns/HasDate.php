<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasDate
{
    protected string | Closure | null $date = null;

    public function date(string | Closure | null $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->evaluate($this->date);
    }
}
