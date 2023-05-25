<?php

namespace Filament\Infolists\Concerns;

use Livewire\Component;

trait BelongsToLivewire
{
    protected ?Component $livewire = null;

    public function livewire(?Component $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): ?Component
    {
        return $this->livewire;
    }
}
