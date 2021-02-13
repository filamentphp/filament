<?php

namespace Filament\Actions\Concerns;

use Exception;
use Filament\Fields\File;
use Filament\Fields\InputField;
use Filament\Fields\Tab;
use Filament\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

trait HasForm
{
    use WithFileUploads;

    public $record;

    public $temporaryUploadedFiles = [];

    public static function getTemporaryUploadedFilePropertyName($fieldName)
    {
        return Str::of($fieldName)
            ->ucfirst()
            ->prepend('temporaryUploadedFiles.');
    }

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

    public function getUploadedFilePath($name)
    {
        $temporaryUploadedFile = $this->getTemporaryUploadedFile($name);

        if ($temporaryUploadedFile) {
            try {
                return $temporaryUploadedFile->temporaryUrl();
            } catch (Exception $exception) {
                return null;
            }
        }

        return $this->getPropertyValue($name);
    }

    public function getTemporaryUploadedFile($name)
    {
        return $this->getPropertyValue(
            static::getTemporaryUploadedFilePropertyName($name)
        );
    }

    public function storeTemporaryUploadedFiles()
    {
        foreach ($this->getForm()->getFields() as $field) {
            if (! $field instanceof File) continue;

            $temporaryUploadedFile = $this->getTemporaryUploadedFile($field->name);
            if (! $temporaryUploadedFile) continue;

            $storeMethod = $field->visibility === 'public' ? 'storePublicly' : 'store';
            $path = $temporaryUploadedFile->{$storeMethod}($field->directory, $field->disk);
            $url = Storage::disk($field->disk)->url($path);
            $this->syncInput($field->name, $url, false);

            $this->clearTemporaryUploadedFile($field->name);
        }
    }

    public function canRemoveUploadedFile($name)
    {
        return (bool) $this->getPropertyValue($name) || $this->getTemporaryUploadedFile($name);
    }

    public function clearTemporaryUploadedFile($name)
    {
        $this->syncInput(
            static::getTemporaryUploadedFilePropertyName($name),
            null,
            false,
        );
    }

    public function removeUploadedFile($name)
    {
        $this->syncInput($name, null, false);
        $this->clearTemporaryUploadedFile($name);
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

    protected function getPropertyDefaults()
    {
        return $this->getForm()->getDefaults();
    }

    protected function getFields()
    {
        $fields = property_exists($this, 'resource') ? static::$resource::fields() : [];

        if (method_exists($this, 'fields')) return array_merge($fields, $this->fields());

        return $fields;
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
