<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class CallToActionBlock extends RichContentCustomBlock
{
    public static function getIcon(): Heroicon
    {
        return Heroicon::CursorArrowRays;
    }

    public static function getId(): string
    {
        return 'call-to-action';
    }

    public static function getLabel(): string
    {
        return 'Call to action';
    }
}
