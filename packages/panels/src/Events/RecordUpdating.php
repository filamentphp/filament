<?php

namespace Filament\Events;

use Filament\Resources\Pages\Page;
use Illuminate\Foundation\Events\Dispatchable;

class RecordUpdating
{
    use Dispatchable;

    public function __construct(
        protected Page $page,
    ) {}

    public function getPage(): Page
    {
        return $this->page;
    }
}
