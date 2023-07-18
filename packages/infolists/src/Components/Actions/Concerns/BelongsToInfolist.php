<?php

namespace Filament\Infolists\Components\Actions\Concerns;

use Exception;
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
        $livewire = $this->getInfolistComponent()->getInfolist()->getLivewire();

        if (! $livewire) {
            throw new Exception('An infolist tried to mount an action but was not mounted to Livewire.');
        }

        return $livewire;
    }

    public function getRecord(): ?Model
    {
        return $this->getInfolistComponent()->getInfolist()->getRecord();
    }
}
