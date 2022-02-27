<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;

class GlobalSearchResult implements Arrayable
{
    public function __construct(
        protected string $title,
        protected string $url,
        protected array $details = [],
    ) {
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'details' => $this->details,
        ];
    }
}
