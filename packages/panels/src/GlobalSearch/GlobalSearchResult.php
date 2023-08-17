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
        readonly public string $title,
        readonly public string $url,
        readonly public array $details = [],
        readonly public array $actions = [],
    ) {
    }
}
