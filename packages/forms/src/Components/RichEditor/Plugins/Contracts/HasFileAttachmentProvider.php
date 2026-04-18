<?php

namespace Filament\Forms\Components\RichEditor\Plugins\Contracts;

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;

interface HasFileAttachmentProvider
{
    public function getFileAttachmentProvider(): ?FileAttachmentProvider;
}
