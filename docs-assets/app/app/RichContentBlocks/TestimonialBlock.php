<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class TestimonialBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'testimonial';
    }

    public static function getLabel(): string
    {
        return 'Testimonial';
    }
}
