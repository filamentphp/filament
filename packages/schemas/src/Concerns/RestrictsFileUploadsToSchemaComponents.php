<?php

namespace Filament\Schemas\Concerns;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Livewire\Attributes\Renderless;
use Livewire\WithFileUploads;

trait RestrictsFileUploadsToSchemaComponents
{
    use WithFileUploads {
        WithFileUploads::_startUpload as private baseStartUpload;
        WithFileUploads::_finishUpload as private baseFinishUpload;
        WithFileUploads::_uploadErrored as private baseUploadErrored;
        WithFileUploads::_removeUpload as private baseRemoveUpload;
    }

    /**
     * @param  string  $name
     * @param  array<mixed>  $fileInfo
     * @param  bool  $isMultiple
     */
    #[Renderless]
    public function _startUpload($name, $fileInfo, $isMultiple): void
    {
        abort_unless($this->isFileUploadForSchemaComponent($name), 403);

        $this->baseStartUpload($name, $fileInfo, $isMultiple);
    }

    /**
     * @param  string  $name
     * @param  array<string>  $tmpPath
     * @param  bool  $isMultiple
     * @param  bool  $append
     */
    public function _finishUpload($name, $tmpPath, $isMultiple, $append = true): void
    {
        abort_unless($this->isFileUploadForSchemaComponent($name), 403);

        $this->baseFinishUpload($name, $tmpPath, $isMultiple, $append);
    }

    /**
     * @param  string  $name
     * @param  ?string  $errorsInJson
     * @param  bool  $isMultiple
     */
    public function _uploadErrored($name, $errorsInJson, $isMultiple): void
    {
        abort_unless($this->isFileUploadForSchemaComponent($name), 403);

        $this->baseUploadErrored($name, $errorsInJson, $isMultiple);
    }

    /**
     * @param  string  $name
     * @param  string  $tmpFilename
     */
    public function _removeUpload($name, $tmpFilename): void
    {
        abort_unless($this->isFileUploadForSchemaComponent($name), 403);

        $this->baseRemoveUpload($name, $tmpFilename);
    }

    protected function isFileUploadForSchemaComponent(string $name): bool
    {
        if (str_starts_with($name, 'componentFileAttachments.')) {
            $name = substr($name, strlen('componentFileAttachments.'));
        }

        $component = $this->getSchemaComponentForFileUpload($name);

        if ($component !== null) {
            return true;
        }

        $lastDotPosition = strrpos($name, '.');

        if ($lastDotPosition === false) {
            return false;
        }

        return $this->getSchemaComponentForFileUpload(substr($name, 0, $lastDotPosition)) !== null;
    }

    protected function getSchemaComponentForFileUpload(string $statePath): ?Component
    {
        foreach ($this->getCachedSchemas() as $schema) {
            if (! $schema instanceof Schema) {
                continue;
            }

            foreach ($schema->getFlatComponents() as $component) {
                if (! $component instanceof Field) {
                    continue;
                }

                if ($component->getStatePath() !== $statePath) {
                    continue;
                }

                if ($component instanceof BaseFileUpload) {
                    return $component;
                }

                if (
                    in_array(HasFileAttachments::class, class_uses_recursive($component), strict: true) &&
                    method_exists($component, 'hasFileAttachments') &&
                    $component->hasFileAttachments()
                ) {
                    return $component;
                }
            }
        }

        return null;
    }
}
