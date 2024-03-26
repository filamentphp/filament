<?php

namespace Filament\Schema\Contracts;

use Filament\Support\Contracts\TranslatableContentDriver;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

interface HasSchemas
{
    public function getFormComponentFileAttachment(string $statePath): ?TemporaryUploadedFile;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver;

    public function getOldFormState(string $statePath): mixed;
}
