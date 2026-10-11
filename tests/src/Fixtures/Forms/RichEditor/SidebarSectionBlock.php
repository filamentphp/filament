<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class SidebarSectionBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'section';
    }
}
