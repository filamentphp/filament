<?php

namespace Filament\GlobalSearch;

class GlobalSearchResult
{
    public function __construct(
        public int|string $key,
        public string $title,
        public string $url,
        public array $details = [],
    ) {
    }
}
