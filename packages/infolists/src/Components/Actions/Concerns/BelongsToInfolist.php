<?php

namespace Filament\Infolists\Components\Actions\Concerns;

use Exception;
use Filament\Infolists\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;

trait BelongsToInfolist
{
    protected ?Component $component = null;

    public function component(?Component $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }

    public function getLivewire(): LivewireComponent
    {
        $livewire = $this->getComponent()->getInfolist()->getLivewire();

        if (! $livewire) {
            throw new Exception('An infolist tried to mount an action but was not mounted to Livewire.');
        }

        return $livewire;
    }

    public function getRecord(): ?Model
    {
        return $this->getComponent()->getInfolist()->getRecord();
    }
}
