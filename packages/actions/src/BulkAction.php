<?php

namespace Filament\Actions;

class BulkAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->bulk();
        $this->accessSelectedRecords();

        $this->extraAttributes([
            'x-bind:disabled' => '! selectedRecords.length',
        ]);
    }
}
