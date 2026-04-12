<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class ImageGalleryBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'image-gallery';
    }

    public static function getLabel(): string
    {
        return 'Image gallery';
    }
}
