<?php

namespace Filament\Forms\Components;

use Closure;

/**
 * @deprecated Use `\Filament\Forms\Components\SpatieMediaLibraryFileUpload` instead, with the `multiple()` method.
 * @see SpatieMediaLibraryFileUpload
 */
class SpatieMediaLibraryMultipleFileUpload extends MultipleFileUpload
{
    protected string | Closure | null $collection = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function collection(string | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    public function getUploadComponent(): Component
    {
        /** @var SpatieMediaLibraryFileUpload $component */
        $component = parent::getUploadComponent();

        return $component->collection($this->getCollection());
    }

    protected function getDefaultUploadComponent(): BaseFileUpload
    {
        return SpatieMediaLibraryFileUpload::make('files');
    }
}
