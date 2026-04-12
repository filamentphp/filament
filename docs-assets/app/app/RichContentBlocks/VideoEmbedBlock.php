<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class VideoEmbedBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'video-embed';
    }

    public static function getLabel(): string
    {
        return 'Video embed';
    }
}
