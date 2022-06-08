<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Concerns\CanReplicateRecords;
use Filament\Support\Actions\Contracts\ReplicatesRecords;

class ReplicateAction extends Action implements ReplicatesRecords
{
    use CanReplicateRecords;
}
