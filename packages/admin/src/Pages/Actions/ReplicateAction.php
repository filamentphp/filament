<?php

namespace Filament\Pages\Actions;

use Filament\Pages\Contracts\HasRecord;
use Filament\Pages\Page;
use Filament\Support\Actions\Concerns\CanReplicateRecords;
use Filament\Support\Actions\Contracts\ReplicatesRecords;
use Illuminate\Database\Eloquent\Model;

class ReplicateAction extends Action implements ReplicatesRecords
{
    use CanReplicateRecords;
}
