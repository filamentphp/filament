<?php

namespace Filament\Events;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Foundation\Events\Dispatchable;

class RecordCreated
{
    use Dispatchable;

    public function __construct(
        protected CreateRecord $page,
    ) {}

    public function getPage(): CreateRecord
    {
        return $this->page;
    }
}
