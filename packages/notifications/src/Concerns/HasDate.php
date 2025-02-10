<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasDate
{
    protected string | Closure | null $date = null;

    protected string | Closure | null $exactDate = null;

    public function date(string | Closure | null $date, ?string $exactDate = null): static
    {
        $this->date = $date;
        $this->exactDate = $exactDate;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->evaluate($this->date);
    }

    public function getExactDate(): ?string
    {
        return $this->evaluate($this->exactDate);
    }
}
