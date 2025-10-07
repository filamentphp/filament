<?php

namespace Filament\Resources\Events;

use Filament\Resources\Pages\Page;
use Illuminate\Foundation\Events\Dispatchable;

class RecordUpdating
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected Page $page,
        protected array $data,
    ) {}

    public function getPage(): Page
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
