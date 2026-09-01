<?php

namespace Filament\GlobalSearch;

use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;

class GlobalSearchResult
{
    /**
     * @var array<Action>
     */
    public readonly array $actions;

    /**
     * @param  array<string, string>  $details
     * @param  array<Action>  $actions
     */
    public function __construct(
        public readonly string | Htmlable $title,
        public readonly string $url,
        public readonly array $details = [],
        array $actions = [],
    ) {
        $this->actions = array_map(
            fn (Action $action) => $action
                ->defaultView(Action::LINK_VIEW)
                ->defaultSize(Size::Small),
            $actions,
        );
    }

    /**
     * @return array<Action>
     */
    public function getVisibleActions(): array
    {
        return array_filter(
            $this->actions,
            fn (Action $action): bool => $action->isVisible(),
        );
    }
}
