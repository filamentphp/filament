<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Livewire\WithFileUploads;

trait CanUploadFiles
{
    use WithFileUploads;

    public $temporaryUploadedFiles = [];

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

    public function removeUploadedFile($name)
    {
        $this->syncInput($name, null, false);
        $this->clearTemporaryUploadedFile($name);
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
