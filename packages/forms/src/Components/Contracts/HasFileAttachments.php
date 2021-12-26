<?php

namespace Filament\Forms\Components\Contracts;

use Livewire\TemporaryUploadedFile;

interface HasFileAttachments
{
    public function saveUploadedFileAttachment(TemporaryUploadedFile $attachment): ?string;
}
