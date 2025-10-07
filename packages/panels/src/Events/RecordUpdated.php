<?php

namespace Filament\Events;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Foundation\Events\Dispatchable;

class RecordUpdated
{
    use Dispatchable;

    public function __construct(
        protected EditRecord $page,
    ) {}

    public function getPage(): EditRecord
    {
        return $this->page;
    }
}
