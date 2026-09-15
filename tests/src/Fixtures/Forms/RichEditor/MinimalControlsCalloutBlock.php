<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class MinimalControlsCalloutBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'callout';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            TextInput::make('message')->required(),
        ]);
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return '<p>' . e($config['message'] ?? '') . '</p>';
    }
}
