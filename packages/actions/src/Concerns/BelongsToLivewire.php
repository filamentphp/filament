<?php

namespace Filament\Actions\Concerns;

use Filament\Actions\Contracts\HasActions;

trait BelongsToLivewire
{
    protected HasActions $livewire;

    public function livewire(HasActions $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): HasActions
    {
        return $this->livewire;
    }
}
