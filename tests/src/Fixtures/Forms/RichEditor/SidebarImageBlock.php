<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class SidebarImageBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'image';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-photo';
    }
}
