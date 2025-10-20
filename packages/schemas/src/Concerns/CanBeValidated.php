<?php

namespace Filament\Schemas\Concerns;

use Filament\Forms\Components;
use Filament\Schemas\Components\Component;

trait CanBeValidated
{
    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        $attributes = [];

        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isNeitherDehydratedNorValidated()) {
                continue;
            }

            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationAttributes($attributes);
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isDirectlyHidden()) {
                    continue;
                }

                $attributes = [
                    ...$attributes,
                    ...$childSchema->getValidationAttributes(),
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

        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isNeitherDehydratedNorValidated()) {
                continue;
            }

            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationMessages($messages);
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isDirectlyHidden()) {
                    continue;
                }

                $messages = [
                    ...$messages,
                    ...$childSchema->getValidationMessages(),
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

        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isNeitherDehydratedNorValidated()) {
                continue;
            }

            if ($component instanceof Components\Contracts\HasValidationRules) {
                $component->dehydrateValidationRules($rules);
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isDirectlyHidden()) {
                    continue;
                }

                $rules = [
                    ...$rules,
                    ...$childSchema->getValidationRules(),
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
            $this->getComponents(withActions: false, withHidden: true),
            fn (Component $component): bool => ! $component->isHiddenAndNotDehydratedWhenHidden(),
        ))) {
            return [];
        }

        $rules = $this->getValidationRules();

        if (! count($rules)) {
            return [];
        }

        $livewire = $this->getLivewire();

        // By storing the currently validating schema in the Livewire component, we can optimize the validation process
        // so that the `prepareForValidation()` method is only called for the current schema instead of all schemas.
        // This can also prevent infinite loops involving schemas that self-validate, such as the table query
        // builder which crashes when it is being used while an action is submitted.
        $livewire->currentlyValidatingSchema($this);

        try {
            return $livewire->validate($rules, $this->getValidationMessages(), $this->getValidationAttributes());
        } finally {
            $livewire->currentlyValidatingSchema(null);
        }
    }
}
