<?php

namespace App\RichContentBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            Textarea::make('quote')->required(),
            TextInput::make('author')->required(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return '<blockquote><p>' . e($config['quote'] ?? '') . '</p><p>— ' . e($config['author'] ?? '') . '</p></blockquote>';
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function shouldApplyProseStylingToPreview(array $config): bool
    {
        return true;
    }
}
