<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Concerns\CanReplicateRecords;
use Illuminate\Database\Eloquent\Model;

class ReplicateAction extends Action
{
    use CanReplicateRecords;
}
