<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class AlertBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'alert';
    }

    public static function getLabel(): string
    {
        return 'Alert';
    }
}
