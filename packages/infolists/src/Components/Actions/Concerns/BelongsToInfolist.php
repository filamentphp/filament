<?php

namespace Filament\Infolists\Components\Actions\Concerns;

use Filament\Infolists\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;

trait BelongsToInfolist
{
    protected ?Component $infolistComponent = null;

    public function component(?Component $component): static
    {
        $this->infolistComponent = $component;

        return $this;
    }

    public function getInfolistComponent(): ?Component
    {
        return $this->infolistComponent;
    }

    public function getLivewire(): LivewireComponent
    {
        return $this->getInfolistComponent()->getInfolist()->getLivewire();
    }

    public function getRecord(): ?Model
    {
        return $this->getInfolistComponent()->getInfolist()->getRecord();
    }
}
