<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileAttachmentProviderStub implements FileAttachmentProvider
{
    public function __construct(
        protected ?RichContentAttribute $attribute = null
    ) {}

    public function attribute(RichContentAttribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        return null;
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        return null;
    }

    public function getDefaultFileAttachmentVisibility(): ?string
    {
        return 'private';
    }

    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
    {
        return false;
    }

    /**
     * @param  array<mixed>  $exceptIds
     */
    public function cleanUpFileAttachments(array $exceptIds): void {}
}
