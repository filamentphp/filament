<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class CallToActionBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'call-to-action';
    }

    public static function getLabel(): string
    {
        return 'Call to action';
    }
}
