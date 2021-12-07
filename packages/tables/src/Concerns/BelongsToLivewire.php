<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Contracts\HasTable;

trait BelongsToLivewire
{
    protected HasTable $livewire;

    public function livewire(HasTable $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): HasTable
    {
        return $this->livewire;
    }
}
