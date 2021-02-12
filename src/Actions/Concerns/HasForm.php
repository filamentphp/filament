<?php

namespace Filament\Actions\Concerns;

use Filament\Fields\InputField;
use Filament\Fields\Tab;
use Filament\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

trait HasForm
{
    use WithFileUploads;

    public $record;

    public $temporaryUploadedFiles = [];

    public function reset(...$properties)
    {
        parent::reset(...$properties);

        $defaults = $this->getPropertyDefaults();

        if (count($properties) && is_array($properties[0])) $properties = $properties[0];

        if (empty($properties)) $properties = array_keys($defaults);

        $propertiesToFill = collect($properties)
            ->filter(fn ($property) => in_array($property, $defaults))
            ->mapWithKeys(fn ($property) => [$property => $defaults[$property]])
            ->toArray();

        $this->fill($propertiesToFill);
    }

    public function getFilePreviewUrl($name)
    {
        $temporaryUploadedFile = $this->getPropertyValue(
            Str::of($name)
                ->ucfirst()
                ->prepend('temporaryUploadedFiles.')
        );

        if ($temporaryUploadedFile) {

        }
    }

    protected function getPropertyDefaults()
    {
        return $this->getForm()->getDefaults();
    }

    public function getForm()
    {
        $record = null;
        if (property_exists($this, 'record') && $this->record instanceof Model) $record = $this->record;

        return new Form(
            $this->getFields(),
            static::class,
            $record,
        );
    }

    protected function getFields()
    {
        $fields = property_exists($this, 'resource') ? static::$resource::fields() : [];

        if (method_exists($this, 'fields')) return array_merge($fields, $this->fields());

        return $fields;
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFields())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof InputField &&
                        array_key_exists($field->name, $exception->validator->failed())
                    );
                });

            if ($fieldToFocus) {
                $possibleTab = $fieldToFocus->parentField;

                while ($possibleTab) {
                    if ($possibleTab instanceof Tab) {
                        $this->dispatchBrowserEvent(
                            'switch-tab',
                            $possibleTab->parentField->id . '.' . $possibleTab->id,
                        );

                        break;
                    }

                    $possibleTab = $possibleTab->parentField;
                }
            }

            throw $exception;
        }
    }

    protected function callFormHooks($event)
    {
        return $this->getForm()->callHook($this, $event);
    }

    protected function fillWithFormDefaults()
    {
        $this->fill($this->getPropertyDefaults());
    }

    protected function getRules()
    {
        $rules = $this->getForm()->getRules();

        foreach (parent::getRules() as $field => $conditions) {
            if (! is_array($conditions)) $conditions = explode('|', $conditions);

            $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
        }

        return $rules;
    }

    protected function getValidationAttributes()
    {
        $attributes = $this->getForm()->getValidationAttributes();

        foreach (parent::getValidationAttributes() as $name => $label) {
            $attributes[$name] = $label;
        }

        return $attributes;
    }
}
