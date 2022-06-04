<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\CanRestoreRecords;
use Filament\Support\Actions\Concerns\HasLifecycleHooks;
use Filament\Support\Actions\Contracts\ReplicatesRecords;

class RestoreAction extends Action
{
    use CanRestoreRecords;
}
