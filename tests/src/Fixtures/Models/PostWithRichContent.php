<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Tests\Fixtures\Forms\RichEditor\PluginWithFileAttachmentProvider;

class PostWithRichContent extends Post implements HasRichContent
{
    use InteractsWithRichContent;

    protected $table = 'posts';

    protected function setUpRichContent(): void
    {
        $this
            ->registerRichContent('content')
            ->plugins([PluginWithFileAttachmentProvider::make()]);
    }
}
