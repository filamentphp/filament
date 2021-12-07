<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Contracts\HasForms;

trait BelongsToLivewire
{
    protected HasForms $livewire;

    public function livewire(HasForms $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): HasForms
    {
        return $this->livewire;
    }
}
