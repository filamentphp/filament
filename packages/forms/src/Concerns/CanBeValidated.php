<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components;

trait CanBeValidated
{
    public function getInvalidComponentToFocus($statePaths = []): ?Components\Component
    {
        foreach ($this->getComponents() as $component) {
            if (in_array($component->getStatePath(), $statePaths)) {
                return $component;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($componentToFocus = $container->getInvalidComponentToFocus($statePaths)) {
                    return $componentToFocus;
                }
            }
        }

        return null;
    }

    public function getValidationAttributes(): array
    {
        $attributes = [];

        foreach ($this->getComponents() as $component) {
            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationAttributes($attributes);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $attributes = array_merge($attributes, $container->getValidationAttributes());
            }
        }

        return $attributes;
    }

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getComponents() as $component) {
            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationRules($rules);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $rules = array_merge($rules, $container->getValidationRules());
            }
        }

        return $rules;
    }

    public function validate(): array
    {
        if (! count($this->getComponents())) {
            return [];
        }

        $rules = $this->getValidationRules();

        if (! count($rules)) {
            return [];
        }

        return $this->getLivewire()->validate($rules, [], $this->getValidationAttributes());
    }
}
