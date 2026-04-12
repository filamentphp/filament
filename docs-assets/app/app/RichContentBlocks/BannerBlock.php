<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class BannerBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'banner';
    }

    public static function getLabel(): string
    {
        return 'Banner';
    }
}
