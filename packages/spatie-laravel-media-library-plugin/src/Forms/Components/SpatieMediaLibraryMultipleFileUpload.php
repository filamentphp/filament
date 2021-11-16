<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class SpatieMediaLibraryMultipleFileUpload extends MultipleFileUpload
{
    protected $collection = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (SpatieMediaLibraryMultipleFileUpload $component) {
            $state = $component->getUploadedFiles();

            $state[(string) Str::uuid()] = [
                'file' => null,
            ];

            $component->state($state);
        });

        $this->dehydrated(false);
    }

    public function getUploadedFiles(): array
    {
        $collection = $this->getCollection();
        $model = $this->getModel();

        if (! $model instanceof HasMedia) {
            return [];
        }

        $files = [];

        foreach ($model->getMedia($collection) as $file) {
            $uuid = $file->uuid;

            $files[$uuid] = [
                'file' => $uuid,
            ];
        }

        return $files;
    }

    public function collection(string | callable $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    protected function getDefaultUploadComponent(): Component
    {
        return SpatieMediaLibraryFileUpload::make('file');
    }
}
