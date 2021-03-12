<?php

namespace Filament\Forms;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tab;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
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

    public function clearTemporaryUploadedFile($name)
    {
        $this->syncInput(
            static::getTemporaryUploadedFilePropertyName($name),
            null,
            false
        );
    }

    public function getTemporaryUploadedFile($name)
    {
        return $this->getPropertyValue(
            static::getTemporaryUploadedFilePropertyName($name)
        );
    }

    public function getUploadedFileUrl($name, $disk)
    {
        $path = $this->getPropertyValue($name);

        if (! $path) return null;

        $storage = Storage::disk($disk);

        if (
            $storage->getDriver()->getAdapter() instanceof AwsS3Adapter &&
            $storage->getVisibility($path) === 'private'
        ) {
            return $storage->temporaryUrl(
                $path,
                now()->addMinutes(5),
            );
        }

        return $storage->url($path);
    }

    public function storeTemporaryUploadedFiles()
    {
        foreach ($this->getForm()->getFlatSchema() as $field) {
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
        $this->clearTemporaryUploadedFile($name);
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
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
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
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
        $field = collect($this->getForm()->getFlatSchema())
            ->first(fn ($field) => $field instanceof Select && $field->name === $fieldName);

        if (! $field) return [];

        return $field->getOptionSearchResults($search);
    }

    public function validateTemporaryUploadedFiles()
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
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
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

    public function focusTabbedField($field)
    {
        if ($field) {
            $possibleTab = $field->parent;

            while ($possibleTab) {
                if ($possibleTab instanceof Tab) {
                    $this->dispatchBrowserEvent(
                        'switch-tab',
                        $possibleTab->parent->getId() . '.' . $possibleTab->getId(),
                    );

                    break;
                }

                $possibleTab = $possibleTab->parent;
            }
        }
    }

    public function getPropertyDefaults()
    {
        return $this->getForm()->getDefaultValues();
    }

    public function fillWithFormDefaults()
    {
        $this->fill($this->getPropertyDefaults());
    }

    public function getRules()
    {
        $rules = $this->getForm()->getRules();

        foreach (parent::getRules() as $field => $conditions) {
            if (! is_array($conditions)) $conditions = explode('|', $conditions);

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
}
