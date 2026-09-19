<?php

namespace App\RichContentBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Support\Icons\Heroicon;

class AlertBlock extends RichContentCustomBlock
{
    public static function getIcon(): Heroicon
    {
        return Heroicon::ExclamationTriangle;
    }

    public static function getId(): string
    {
        return 'alert';
    }

    public static function getLabel(): string
    {
        return 'Alert';
    }
}
