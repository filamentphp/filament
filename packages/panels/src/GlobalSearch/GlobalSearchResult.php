<?php

namespace Filament\GlobalSearch;

use Filament\GlobalSearch\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class GlobalSearchResult
{
    /**
     * @param  array<string, string>  $details
     * @param  array<Action>  $actions
     */
    public function __construct(
        readonly public string | Htmlable $title,
        readonly public string $url,
        readonly public array $details = [],
        readonly public array $actions = [],
    ) {
    }
}
