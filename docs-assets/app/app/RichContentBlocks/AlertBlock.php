<?php

namespace App\RichContentBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;
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

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            TextInput::make('message')->required(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return '<p>' . e($config['message'] ?? '') . '</p>';
    }
}
