<?php

namespace Filament\Actions\Contracts;

use Livewire\Component;

interface HasLivewire
{
    public function livewire(Component $livewire): static;

    public function getLivewire(): object;
}
