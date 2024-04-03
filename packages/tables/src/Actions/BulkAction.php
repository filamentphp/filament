<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Action;

class BulkAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->accessSelectedRecords();

        $this->extraAttributes([
            'x-bind:disabled' => '! selectedRecords.length',
        ]);
    }
}
