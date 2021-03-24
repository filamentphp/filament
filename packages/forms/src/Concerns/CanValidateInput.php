<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Concerns\CanConcealFields;
use Filament\Forms\Components\Field;
use Illuminate\Validation\ValidationException;

trait CanValidateInput
{
    public function focusConcealedField($field)
    {
        $possiblyConcealingComponent = $field->getParent();

        while ($possiblyConcealingComponent) {
            if (in_array(
                CanConcealFields::class,
                class_uses_recursive($possiblyConcealingComponent),
            )) {
                $this->dispatchBrowserEvent(
                    'open',
                    $possiblyConcealingComponent->getId(),
                );

                break;
            }

            $possiblyConcealingComponent = $possiblyConcealingComponent->getParent();
        }
    }

    public function getRules()
    {
        $rules = $this->getForm()->getRules();

        foreach (parent::getRules() as $field => $conditions) {
            if (! is_array($conditions)) {
                $conditions = explode('|', $conditions);
            }

            $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        $attributes = $this->getForm()->getValidationAttributes();

        foreach (parent::getValidationAttributes() as $name => $label) {
            $attributes[$name] = $label;
        }

        return $attributes;
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof Field &&
                        array_key_exists($field->getName(), $exception->validator->failed())
                    );
                });

            if ($fieldToFocus) {
                $this->focusConcealedField($fieldToFocus);
            }

            throw $exception;
        }
    }

    public function validateOnly($field, $rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validateOnly($field, $rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof Field &&
                        array_key_exists($field->getName(), $exception->validator->failed())
                    );
                });

            if ($fieldToFocus) {
                $this->focusConcealedField($fieldToFocus);
            }

            throw $exception;
        }
    }
}
