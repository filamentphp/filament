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
                $attributes[$component->getStatePath()] = $component->getValidationAttribute();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                $attributes = array_merge($attributes, $container->getValidationAttributes());
            }
        }

        return $attributes;
    }

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getComponents() as $component) {
            if (
                $component instanceof Components\Contracts\HasValidationRules &&
                count($componentRules = $component->getValidationRules())
            ) {
                $rules[$component->getStatePath()] = $componentRules;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                $rules = array_merge($rules, $container->getValidationRules());
            }
        }

        return $rules;
    }

    public function validate(): array
    {
        return $this->getLivewire()->validate($this->getValidationRules(), [], $this->getValidationAttributes());
    }
}
