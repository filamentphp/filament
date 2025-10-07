<?php

namespace Filament\Resources\Events;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Foundation\Events\Dispatchable;

class RecordCreating
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected CreateRecord $page,
        protected array $data,
    ) {}

    public function getPage(): CreateRecord
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
