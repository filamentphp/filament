<?php

namespace Filament\GlobalSearch;

use Filament\GlobalSearch\Actions\Action;

class GlobalSearchResult
{
    /**
     * @param  array<string, string>  $details
     * @param  array<Action>  $actions
     */
    public function __construct(
        public string $title,
        public string $url,
        public array $details = [],
        public array $actions = [],
    ) {
    }
}
