<?php

namespace Filament\Actions\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

interface HasLivewire
{
    public function livewire(Component $livewire): static;
}
