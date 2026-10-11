<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class SidebarQuoteBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'quote';
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::ChatBubbleLeftRight;
    }
}
