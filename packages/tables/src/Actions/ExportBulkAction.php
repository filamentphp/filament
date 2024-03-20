<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanExportRecords;

class ExportBulkAction extends BulkAction
{
    use CanExportRecords {
        setUp as baseSetUp;
    }

    protected function setUp(): void
    {
        $this->baseSetUp();

        $this->fetchSelectedRecords(false);
    }
}
