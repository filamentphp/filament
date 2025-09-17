<?php

namespace Filament\Resources\Resource\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;

trait HasLabels
{
    /**
     * @deprecated Use `$modelLabel` instead.
     */
    protected static ?string $label = null;

    protected static ?string $modelLabel = null;

    /**
     * @deprecated Use `$pluralModelLabel` instead.
     */
    protected static ?string $pluralLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $recordTitleAttribute = null;

    protected static bool $hasTitleCaseModelLabel = true;

    /**
     * @deprecated Use `getModelLabel()` instead.
     */
    public static function getLabel(): ?string
    {
        return static::$label;
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? static::getLabel() ?? get_model_label(static::getModel());
    }

    public static function getTitleCaseModelLabel(): string
    {
        if (! static::hasTitleCaseModelLabel()) {
            return static::getModelLabel();
        }

        return Str::ucwords(static::getModelLabel());
    }

    /**
     * @deprecated Use `getPluralModelLabel()` instead.
     */
    public static function getPluralLabel(): ?string
    {
        return static::$pluralLabel;
    }

    public static function getPluralModelLabel(): string
    {
        if (filled($label = static::$pluralModelLabel ?? static::getPluralLabel())) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return Str::plural(static::getModelLabel());
        }

        return static::getModelLabel();
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        if (! static::hasTitleCaseModelLabel()) {
            return static::getPluralModelLabel();
        }

        return Str::ucwords(static::getPluralModelLabel());
    }

    public static function titleCaseModelLabel(bool $condition = true): void
    {
        static::$hasTitleCaseModelLabel = $condition;
    }

    public static function hasTitleCaseModelLabel(): bool
    {
        return static::$hasTitleCaseModelLabel;
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    public static function getRecordTitle(?Model $record): string | Htmlable | null
    {
        return $record?->getAttribute(static::getRecordTitleAttribute()) ?? static::getModelLabel();
    }

    public static function hasRecordTitle(): bool
    {
        return static::getRecordTitleAttribute() !== null;
    }
}
