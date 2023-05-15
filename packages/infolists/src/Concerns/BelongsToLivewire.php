<?php

namespace Filament\Infolists\Concerns;

use Exception;
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
        if (! $this->livewire) {
            throw new Exception('An infolist tried to access Livewire but was not mounted.]');
        }

        return $this->livewire;
    }
}
