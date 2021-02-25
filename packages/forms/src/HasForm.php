<?php

namespace Filament\Forms;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tab;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

trait HasForm
{
    use WithFileUploads;

    public $temporaryUploadedFiles = [];

    public static function getTemporaryUploadedFilePropertyName($fieldName)
    {
        return "temporaryUploadedFiles.{$fieldName}";
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

    public function getTemporaryUploadedFile($name)
    {
        return $this->getPropertyValue(
            static::getTemporaryUploadedFilePropertyName($name)
        );
    }

    public function getSelectFieldDisplayValue($fieldName, $value)
    {
        $field = collect($this->getForm()->getSchema())
            ->first(fn ($field) => $field instanceof Select && $field->name === $fieldName);

        if (! $field) return [];

        return $field->getDisplayValue($value);
    }

    public function getUploadedFileUrl($name, $disk)
    {
        $path = $this->getPropertyValue($name);

        if (! $path) return null;

        return Storage::disk($disk)->url($path);
    }

    public function storeTemporaryUploadedFiles()
    {
        foreach ($this->getForm()->getSchema() as $field) {
            if (! $field instanceof FileUpload) continue;

            $temporaryUploadedFile = $this->getTemporaryUploadedFile($field->name);
            if (! $temporaryUploadedFile) continue;

            $storeMethod = $field->visibility === 'public' ? 'storePublicly' : 'store';
            $path = $temporaryUploadedFile->{$storeMethod}($field->directory, $field->disk);
            $this->syncInput($field->name, $path, false);
        }

        $this->resetTemporaryUploadedFiles();
    }

    public function resetTemporaryUploadedFiles()
    {
        $this->temporaryUploadedFiles = [];
    }

    public function removeUploadedFile($name)
    {
        $this->syncInput($name, null, false);
        $this->syncInput(
            static::getTemporaryUploadedFilePropertyName($name),
            null,
            false,
        );
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getSchema())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof Field &&
                        array_key_exists($field->name, $exception->validator->failed())
                    );
                });

            if ($fieldToFocus) $this->focusTabbedField($fieldToFocus);

            throw $exception;
        }
    }

    public function validateOnly($field, $rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validateOnly($field, $rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getSchema())
                ->first(function ($field) use ($exception) {
                    return ($field instanceof Field &&
                        array_key_exists($field->name, $exception->validator->failed())
                    );
                });

            if ($fieldToFocus) $this->focusTabbedField($fieldToFocus);

            throw $exception;
        }
    }

    public function getSelectFieldOptionSearchResults($fieldName, $search = '')
    {
        $field = collect($this->getForm()->getSchema())
            ->first(fn ($field) => $field instanceof Select && $field->name === $fieldName);

        if (! $field) return [];

        return $field->getOptionSearchResults($search);
    }

    protected function validateTemporaryUploadedFiles()
    {
        $rules = collect($this->getRules())
            ->filter(function ($conditions, $field) {
                return Str::of($field)->startsWith('temporaryUploadedFiles.');
            })
            ->toArray();

        if (! count($rules)) return;

        try {
            return parent::validate($rules);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getSchema())
                ->first(function ($field) use ($exception) {
                    return (
                        $field instanceof Field &&
                        array_key_exists(
                            static::getTemporaryUploadedFilePropertyName($field->name),
                            $exception->validator->failed()
                        )
                    );
                });

            if ($fieldToFocus) $this->focusTabbedField($fieldToFocus);

            $this->setErrorBag($exception->validator->errors());

            foreach ($this->getErrorBag()->messages() as $field => $messages) {
                $field = (string) Str::of($field)->after('temporaryUploadedFiles.');

                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            throw $exception;
        }
    }

    protected function focusTabbedField($field)
    {
        if ($field) {
            $possibleTab = $field->parent;

            while ($possibleTab) {
                if ($possibleTab instanceof Tab) {
                    $this->dispatchBrowserEvent(
                        'switch-tab',
                        $possibleTab->parent->id . '.' . $possibleTab->id,
                    );

                    break;
                }

                $possibleTab = $possibleTab->parent;
            }
        }
    }

    protected function getPropertyDefaults()
    {
        return $this->getForm()->getDefaults();
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
