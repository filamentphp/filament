<?php

namespace Filament\Infolists\Components;

use Filament\Infolists\ComponentContainer;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\StatePathSegment;
use Illuminate\Database\Eloquent\Model;

class RepeatableEntry extends Entry
{
    use CanBeContained;
    use Concerns\HasContainerGridLayout;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.repeatable-entry';

    /**
     * @return array<ComponentContainer>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

        $state = collect($this->getState() ?? [])->all();
        $containers = [];
        $usedItemStatePaths = [];

        foreach (array_keys($state) as $itemKey) {
            if (StatePathSegment::isSafe($itemKey)) {
                $usedItemStatePaths[(string) $itemKey] = true;
            }
        }

        foreach ($state as $itemKey => $itemData) {
            $itemStatePath = StatePathSegment::isSafe($itemKey)
                ? $itemKey
                : $this->generateSafeItemStatePath($itemKey, $usedItemStatePaths);

            $container = $this
                ->getChildComponentContainer()
                ->getClone()
                ->statePath($itemStatePath)
                ->inlineLabel(false);

            if ($itemData instanceof Model) {
                $container->record($itemData);
            } elseif (is_array($itemData)) {
                $container->state($itemData);
            }

            $containers[$itemKey] = $container;
        }

        return $containers;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, true>  $usedItemStatePaths
     */
    protected function generateSafeItemStatePath(string | int $itemKey, array &$usedItemStatePaths): string
    {
        $baseItemStatePath = 'item-' . hash('sha256', (string) $itemKey);
        $itemStatePath = $baseItemStatePath;
        $itemStatePathSuffix = 1;

        while (isset($usedItemStatePaths[$itemStatePath])) {
            $itemStatePath = "{$baseItemStatePath}-{$itemStatePathSuffix}";
            $itemStatePathSuffix++;
        }

        $usedItemStatePaths[$itemStatePath] = true;

        return $itemStatePath;
    }
}
