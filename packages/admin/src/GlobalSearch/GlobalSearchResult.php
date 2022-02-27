<?php

namespace Filament\GlobalSearch;

use Illuminate\Contracts\Support\Arrayable;

class GlobalSearchResult
{
    public function __construct(
        public string $title,
        public string $url,
        public array $details = [],
    ) {
    }
}
