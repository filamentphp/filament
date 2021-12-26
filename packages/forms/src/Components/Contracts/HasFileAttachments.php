<?php

namespace Filament\Forms\Components\Contracts;

use Livewire\TemporaryUploadedFile;
use SplFileInfo;

interface HasFileAttachments
{
    public function saveUploadedFileAttachment(TemporaryUploadedFile $attachment): ?string;
}
