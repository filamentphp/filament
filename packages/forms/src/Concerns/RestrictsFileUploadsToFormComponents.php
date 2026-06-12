<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\HasFileAttachments;
use Filament\Forms\Components\Field;
use Livewire\WithFileUploads;

trait RestrictsFileUploadsToFormComponents
{
    use WithFileUploads {
        WithFileUploads::_startUpload as private baseStartUpload;
        WithFileUploads::_finishUpload as private baseFinishUpload;
    }

    /**
     * @param  string  $name
     * @param  array<mixed>  $fileInfo
     * @param  bool  $isMultiple
     */
    public function _startUpload($name, $fileInfo, $isMultiple): void
    {
        abort_unless($this->isFileUploadForFormComponent($name), 403);

        $this->baseStartUpload($name, $fileInfo, $isMultiple);
    }

    /**
     * @param  string  $name
     * @param  array<string>  $tmpPath
     * @param  bool  $isMultiple
     * @param  bool  $append
     */
    public function _finishUpload($name, $tmpPath, $isMultiple, $append = false): void
    {
        abort_unless($this->isFileUploadForFormComponent($name), 403);

        $this->baseFinishUpload($name, $tmpPath, $isMultiple, $append);
    }

    protected function isFileUploadForFormComponent(string $name): bool
    {
        if (str_starts_with($name, 'componentFileAttachments.')) {
            $name = substr($name, strlen('componentFileAttachments.'));
        }

        $component = $this->getFormComponentForFileUpload($name);

        if ($component !== null) {
            return true;
        }

        $lastDotPosition = strrpos($name, '.');

        if ($lastDotPosition === false) {
            return false;
        }

        return $this->getFormComponentForFileUpload(substr($name, 0, $lastDotPosition)) !== null;
    }

    protected function getFormComponentForFileUpload(string $statePath): ?Component
    {
        foreach ($this->getCachedForms() as $form) {
            foreach ($form->getFlatComponents() as $component) {
                if (! $component instanceof Field) {
                    continue;
                }

                if ($component->getStatePath() !== $statePath) {
                    continue;
                }

                if ($component instanceof BaseFileUpload) {
                    return $component;
                }

                if ($component instanceof HasFileAttachments) {
                    return $component;
                }
            }
        }

        return null;
    }
}
