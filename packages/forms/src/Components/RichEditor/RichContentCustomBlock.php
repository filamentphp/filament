<?php

namespace Filament\Forms\Components\RichEditor;

use BackedEnum;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

abstract class RichContentCustomBlock
{
    abstract public static function getId(): string;

    public static function getIcon(): string | BackedEnum | Htmlable | null
    {
        return null;
    }

    public static function getLabel(): string
    {
        return (string) str(static::getId())
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): ?string
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return static::getLabel();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): ?string
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function shouldApplyProseStylingToPreview(array $config): bool
    {
        return false;
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->modalHidden();
    }
}
