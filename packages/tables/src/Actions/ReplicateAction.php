<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanReplicateRecords;
use Filament\Actions\Contracts\ReplicatesRecords;
use Filament\Support\Facades\FilamentIcon;

class ReplicateAction extends Action implements ReplicatesRecords
{
    use CanReplicateRecords {
        setUp as baseSetUp;
    }

    protected function setUp(): void
    {
        $this->baseSetUp();

        $this->icon(FilamentIcon::resolve('actions::replicate-action') ?? 'heroicon-m-square-2-stack');
    }
}
