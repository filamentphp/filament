<?php

namespace Filament\Schemas\Concerns;

use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

trait BelongsToLivewire
{
    protected (Component & HasSchemas) | null $livewire = null;

    public function livewire((Component & HasSchemas) | null $livewire = null): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): Component & HasSchemas
    {
        return $this->livewire;
    }
}
