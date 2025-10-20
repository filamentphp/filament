<?php

namespace Filament\Actions;

class BulkAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->bulk();
        $this->accessSelectedRecords();
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAttributes(): array
    {
        return [
            'x-cloak' => true,
            'x-show' => 'getSelectedRecordsCount()',
            ...parent::getExtraAttributes(),
        ];
    }
}
