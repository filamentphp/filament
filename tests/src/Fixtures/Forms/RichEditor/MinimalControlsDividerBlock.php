<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class MinimalControlsDividerBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'divider';
    }
}
