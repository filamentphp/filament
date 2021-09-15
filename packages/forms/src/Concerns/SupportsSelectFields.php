<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Select;

trait SupportsSelectFields
{
    public function getSelectOptionLabel(string $statePath): ?string
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof Select && $component->getStatePath() === $statePath) {
                return $component->getOptionLabel();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getSelectOptionLabel($statePath)) {
                    return $results;
                }
            }
        }

        return null;
    }

    public function getSelectSearchResults(string $statePath, string $query): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof Select && $component->getStatePath() === $statePath) {
                return $component->getSearchResults($query);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getSelectSearchResults($statePath, $query)) {
                    return $results;
                }
            }
        }

        return [];
    }
}
