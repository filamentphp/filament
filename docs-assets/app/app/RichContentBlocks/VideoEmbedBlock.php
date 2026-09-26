<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class VideoEmbedBlock extends RichContentCustomBlock
{
    public static function getIcon(): Heroicon
    {
        return Heroicon::PlayCircle;
    }

    public static function getId(): string
    {
        return 'video-embed';
    }

    public static function getLabel(): string
    {
        return 'Video embed';
    }
}
