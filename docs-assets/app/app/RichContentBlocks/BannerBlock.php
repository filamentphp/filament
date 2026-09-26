<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class BannerBlock extends RichContentCustomBlock
{
    public static function getIcon(): Heroicon
    {
        return Heroicon::Megaphone;
    }

    public static function getId(): string
    {
        return 'banner';
    }

    public static function getLabel(): string
    {
        return 'Banner';
    }
}
