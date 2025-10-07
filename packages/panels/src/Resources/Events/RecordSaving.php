<?php

namespace Filament\Resources\Events;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Foundation\Events\Dispatchable;

class RecordSaving
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected EditRecord $page,
        protected array $data,
    ) {}

    public function getPage(): EditRecord
    {
        return $this->page;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->array;
    }
}
