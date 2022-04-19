<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\MultiSelect;

trait SupportsMultiSelectFields
{
    public function getMultiSelectOptionLabels(string $statePath): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof MultiSelect && $component->getStatePath() === $statePath) {
                return $component->getOptionLabels();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getMultiSelectOptionLabels($statePath)) {
                    return $results;
                }
            }
        }

        return [];
    }

    public function getMultiSelectOptions(string $statePath): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof MultiSelect && $component->getStatePath() === $statePath) {
                return $component->getOptions();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getMultiSelectOptions($statePath)) {
                    return $results;
                }
            }
        }

        return [];
    }

    public function getMultiSelectSearchResults(string $statePath, string $searchQuery): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof MultiSelect && $component->getStatePath() === $statePath) {
                return $component->getSearchResults($searchQuery);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getMultiSelectSearchResults($statePath, $searchQuery)) {
                    return $results;
                }
            }
        }

        return [];
    }
}
