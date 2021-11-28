<?php

namespace Filament\Resources\Concerns;

trait Translatable
{
    public static function getDefaultTranslatableLocale(): string
    {
        return static::getTranslatableLocales()[0];
    }

    public static function getTranslatableAttributes(): array
    {
        $model = static::getModel();

        if (! method_exists($model, 'getTranslatableAttributes')) {
            throw new Exception("Model [{$model}] must use trait [Spatie\Translatable\HasTranslations].");
        }

        $attributes = (new $model())->getTranslatableAttributes();

        if (! count($attributes)) {
            throw new Exception("Model [{$model}] must have [\$translatable] properties defined.");
        }

        return $attributes;
    }

    public static function getTranslatableLocales(): array
    {
        return config('filament-spatie-laravel-translatable-plugin.default_locales');
    }
}
