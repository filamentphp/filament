<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class TestimonialBlock extends RichContentCustomBlock
{
    public static function getIcon(): Heroicon
    {
        return Heroicon::ChatBubbleLeftRight;
    }

    public static function getId(): string
    {
        return 'testimonial';
    }

    public static function getLabel(): string
    {
        return 'Testimonial';
    }
}
