<?php

namespace Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts;

use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

interface FileAttachmentProvider
{
    public function attribute(RichContentAttribute $attribute): static;

    public function getFileAttachmentUrl(mixed $file): ?string;

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed;

    public function getDefaultFileAttachmentVisibility(): ?string;

    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool;

    /**
     * @param  array<mixed>  $exceptIds
     */
    public function cleanUpFileAttachments(array $exceptIds): void;
}
