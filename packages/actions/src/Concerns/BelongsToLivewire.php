<?php

namespace Filament\Actions\Concerns;

use Livewire\Component;

trait BelongsToLivewire
{
    protected Component $livewire;

    public function livewire(Component $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): object
    {
        return $this->livewire;
    }
}
