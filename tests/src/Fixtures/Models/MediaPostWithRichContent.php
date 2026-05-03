<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Tests\Fixtures\Forms\RichEditor\PluginWithFileAttachmentProvider;

class MediaPostWithRichContent extends MediaPost implements HasRichContent
{
    use InteractsWithRichContent;

    protected function setUpRichContent(): void
    {
        $this
            ->registerRichContent('content')
            ->plugins([
                PluginWithFileAttachmentProvider::make(
                    SpatieMediaLibraryFileAttachmentProvider::make(),
                ),
            ]);
    }
}
