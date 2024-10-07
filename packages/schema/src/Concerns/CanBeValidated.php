<?php

namespace Filament\Schema\Concerns;

use Filament\Forms\Components;
use Filament\Schema\Components\Component;

trait CanBeValidated
{
    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        $attributes = [];

        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
                continue;
            }

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
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        $messages = [];

        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
                continue;
            }

            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationMessages($messages);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $messages = [
                    ...$messages,
                    ...$container->getValidationMessages(),
                ];
            }
        }

        return $messages;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isNeitherDehydratedNorValidated()) {
                continue;
            }

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
        if (! count(array_filter(
            $this->getComponents(withHidden: true),
            fn (Component $component): bool => ! $component->isHiddenAndNotDehydratedWhenHidden(),
        ))) {
            return [];
        }

        $rules = $this->getValidationRules();

        if (! count($rules)) {
            return [];
        }

        return $this->getLivewire()->validate($rules, $this->getValidationMessages(), $this->getValidationAttributes());
    }
}
