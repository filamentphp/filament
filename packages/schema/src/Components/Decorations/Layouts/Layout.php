<?php

namespace Filament\Schema\Components\Decorations\Layouts;

use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Support\Components\ViewComponent;

abstract class Layout extends ViewComponent
{
    /**
     * @return array<Action>
     */
    abstract public function getActions(): array;

    abstract public function hasDecorations(): bool;

    /**
     * @param  array<Decoration | Action>  $decorations
     * @return array<Decoration | Action>
     */
    protected function extractActionsFromDecorations(array $decorations): array
    {
        return array_filter(
            $decorations,
            fn (Decoration | Action $decoration): bool => $decoration instanceof Action,
        );
    }

    /**
     * @param  array<Decoration | Action>  $decorations
     * @return array<Decoration | Action>
     */
    protected function filterHiddenActionsFromDecorations(array $decorations): array
    {
        return array_filter(
            $decorations,
            fn (Decoration | Action $decoration): bool => (! ($decoration instanceof Action)) || $decoration->isVisible(),
        );
    }
}
