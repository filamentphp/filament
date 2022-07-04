<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanReplicateRecords;
use Filament\Support\Actions\Contracts\ReplicatesRecords;

class ReplicateAction extends Action implements ReplicatesRecords
{
    use CanReplicateRecords {
        setUp as baseSetUp;
    }

    protected function setUp(): void
    {
        $this->baseSetUp();

        $this->icon('heroicon-s-duplicate');
    }
}
