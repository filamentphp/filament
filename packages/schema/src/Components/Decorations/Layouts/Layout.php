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
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     * @return array<Decoration | Action | array<Decoration | Action>>
     */
    protected function extractActionsFromDecorations(array $decorations): array
    {
        $actions = [];

        foreach ($decorations as $decoration) {
            if (is_array($decoration)) {
                $actions = [
                    ...$actions,
                    ...$this->extractActionsFromDecorations($decoration),
                ];

                continue;
            }

            if (! ($decoration instanceof Action)) {
                continue;
            }

            $actions[] = $decoration;
        }

        return $actions;
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     * @return array<Decoration | Action | array<Decoration | Action>>
     */
    protected function filterHiddenActionsFromDecorations(array $decorations): array
    {
        return array_reduce(
            $decorations,
            function (array $carry, Decoration | Action | array $decoration): array {
                if ($decoration instanceof Action && (! $decoration->isVisible())) {
                    return $carry;
                }

                if (is_array($decoration)) {
                    $carry[] = $this->filterHiddenActionsFromDecorations($decoration);
                } else {
                    $carry[] = $decoration;
                }

                return $carry;
            },
            initial: [],
        );
    }
}
