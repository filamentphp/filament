<?php

namespace Filament\Schema\Components\Decorations\Layouts;

use Filament\Actions\Action;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Concerns\BelongsToContainer;
use Filament\Support\Components\ViewComponent;

abstract class DecorationsLayout extends ViewComponent
{
    use BelongsToContainer;

    abstract public function hasDecorations(): bool;

    /**
     * @return array<string, Action>
     */
    abstract public function getActions(): array;

    /**
     * @param  array<Component | Action | array<Component | Action>>  $decorations
     * @return array<string, Component | Action | array<Component | Action>>
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

            $actions[$decoration->getName()] = $decoration;
        }

        return $actions;
    }

    /**
     * @param  array<Component | Action | array<Component | Action>>  $decorations
     * @return array<Component | Action | array<Component | Action>>
     */
    protected function prepareDecorations(array $decorations): array
    {
        return array_reduce(
            $decorations,
            function (array $carry, Component | Action | array $decoration): array {
                if ($decoration instanceof Action && (! $decoration->isVisible())) {
                    return $carry;
                }

                if (is_array($decoration)) {
                    $carry[] = $this->prepareDecorations($decoration);
                } else {
                    if ($decoration instanceof Component) {
                        $decoration->container($this->getContainer());
                    }

                    $carry[] = $decoration;
                }

                return $carry;
            },
            initial: [],
        );
    }
}
