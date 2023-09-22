<?php

namespace Filament\Panel\Concerns;

trait HasGuests
{
    protected bool $allowGuests = false;

    public function allowGuests(bool $allowGuests = true): static
    {
        $this->allowGuests = $allowGuests;

        return $this;
    }

    public function getAllowGuests(): bool
    {
        return $this->allowGuests;
    }
}
