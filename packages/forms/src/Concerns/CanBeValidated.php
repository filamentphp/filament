<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components;

trait CanBeValidated
{
    /**
     * @return array<string, string>
     */
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

                $attributes = [
                    ...$attributes,
                    ...$container->getValidationAttributes(),
                ];
            }
        }

        return $attributes;
    }

    /**
     * @return array<string, array<mixed>>
     */
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

                $rules = [
                    ...$rules,
                    ...$container->getValidationRules(),
                ];
            }
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
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
