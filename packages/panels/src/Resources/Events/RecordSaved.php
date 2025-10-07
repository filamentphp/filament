<?php

namespace Filament\Resources\Events;

use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class RecordSaved
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected Model $record,
        protected array $data,
        protected Page $page,
    ) {}

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
