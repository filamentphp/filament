<?php

namespace Filament\Forms;

use Filament\Forms\Components\Concerns\CanConcealFields;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Livewire\WithFileUploads;

trait HasForm
{
    use WithFileUploads;

    protected $form;

    public $temporaryUploadedFiles = [];

    public function clearTemporaryUploadedFile($name)
    {
        $this->syncInput(
            static::getTemporaryUploadedFilePropertyName($name),
            null,
            false
        );
    }

    public function fillWithFormDefaults()
    {
        $this->fill($this->getPropertyDefaults());
    }

    public function focusConcealedField($field)
    {
        if ($field) {
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
    }

    protected function form(Form $form)
    {
        return $form;
    }

    public function getForm()
    {
        if ($this->form !== null) {
            return $this->form;
        }

        return $this->form = $this->form(
            Form::for($this),
        );
    }

    public function getPropertyDefaults()
    {
        return $this->getForm()->getDefaultValues();
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

    public function getSelectFieldOptionSearchResults($fieldName, $search = '')
    {
        $field = collect($this->getForm()->getFlatSchema())
            ->first(fn ($field) => $field instanceof Select && $field->getName() === $fieldName);

        if (! $field) {
            return [];
        }

        return $field->getOptionSearchResults($search);
    }

    public function getTemporaryUploadedFile($name)
    {
        return $this->getPropertyValue(
            static::getTemporaryUploadedFilePropertyName($name)
        );
    }

    public static function getTemporaryUploadedFilePropertyName($fieldName)
    {
        return "temporaryUploadedFiles.{$fieldName}";
    }

    public function getUploadedFileUrl($name, $disk)
    {
        $path = $this->getPropertyValue($name);

        if (! $path) {
            return null;
        }

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

    public function getValidationAttributes()
    {
        $attributes = $this->getForm()->getValidationAttributes();

        foreach (parent::getValidationAttributes() as $name => $label) {
            $attributes[$name] = $label;
        }

        return $attributes;
    }

    public function removeUploadedFile($name)
    {
        $this->syncInput($name, null, false);
        $this->clearTemporaryUploadedFile($name);
    }

    public function reset(...$properties)
    {
        parent::reset(...$properties);

        $defaults = $this->getPropertyDefaults();

        if (count($properties) && is_array($properties[0])) {
            $properties = $properties[0];
        }

        if (empty($properties)) {
            $properties = array_keys($defaults);
        }

        $propertiesToFill = collect($properties)
            ->filter(fn ($property) => in_array($property, $defaults))
            ->mapWithKeys(fn ($property) => [$property => $defaults[$property]])
            ->toArray();

        $this->fill($propertiesToFill);
    }

    public function resetTemporaryUploadedFiles()
    {
        $this->temporaryUploadedFiles = [];
    }

    public function storeTemporaryUploadedFiles()
    {
        foreach ($this->getForm()->getFlatSchema() as $field) {
            if (! $field instanceof FileUpload) {
                continue;
            }

            $temporaryUploadedFile = $this->getTemporaryUploadedFile($field->getName());
            if (! $temporaryUploadedFile) {
                continue;
            }

            $storeMethod = $field->getVisibility() === 'public' ? 'storePublicly' : 'store';
            $path = $temporaryUploadedFile->{$storeMethod}($field->getDirectory(), $field->getDiskName());
            $this->syncInput($field->getName(), $path, false);
        }

        $this->resetTemporaryUploadedFiles();
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

    public function validateTemporaryUploadedFiles()
    {
        $rules = collect($this->getRules())
            ->filter(function ($conditions, $field) {
                return Str::of($field)->startsWith('temporaryUploadedFiles.');
            })
            ->toArray();

        if (! count($rules)) {
            return;
        }

        try {
            return parent::validate($rules);
        } catch (ValidationException $exception) {
            $fieldToFocus = collect($this->getForm()->getFlatSchema())
                ->first(function ($component) use ($exception) {
                    return (
                        $component instanceof Field &&
                        array_key_exists(
                            static::getTemporaryUploadedFilePropertyName($component->getName()),
                            $exception->validator->failed()
                        )
                    );
                });

            if ($fieldToFocus) {
                $this->focusConcealedField($fieldToFocus);
            }

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
}
