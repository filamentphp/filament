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

    public function getSelectOptions(string $statePath): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof Select && $component->getStatePath() === $statePath) {
                return $component->getOptions();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getSelectOptions($statePath)) {
                    return $results;
                }
            }
        }

        return [];
    }

    public function getSelectSearchResults(string $statePath, string $searchQuery): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof Select && $component->getStatePath() === $statePath) {
                return $component->getSearchResults($searchQuery);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($results = $container->getSelectSearchResults($statePath, $searchQuery)) {
                    return $results;
                }
            }
        }

        return [];
    }
}
