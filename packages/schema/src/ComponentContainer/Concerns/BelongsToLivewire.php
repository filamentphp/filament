<?php

namespace Filament\Schema\ComponentContainer\Concerns;

use Filament\Schema\Contracts\HasSchemas;
use Livewire\Component;

trait BelongsToLivewire
{
    protected Component & HasSchemas $livewire;

    public function livewire(Component & HasSchemas $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): Component & HasSchemas
    {
        return $this->livewire;
    }
}
