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
        public readonly string | Htmlable $title,
        public readonly string $url,
        public readonly array $details = [],
        public readonly array $actions = [],
    ) {}
}
