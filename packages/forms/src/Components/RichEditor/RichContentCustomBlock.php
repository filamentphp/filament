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
     * @param  array<string, mixed>  $configuration
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $configuration, array $data): ?string
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $configuration
     */
    public static function getPreviewLabel(array $configuration): string
    {
        return static::getLabel();
    }

    /**
     * @param  array<string, mixed>  $configuration
     */
    public static function toPreviewHtml(array $configuration): ?string
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $configuration
     */
    public static function shouldApplyProseStylingToPreview(array $configuration): bool
    {
        return false;
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->modalHidden();
    }
}
