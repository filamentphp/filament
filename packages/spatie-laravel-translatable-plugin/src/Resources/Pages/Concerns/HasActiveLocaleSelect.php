<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Actions\SelectAction;

trait HasActiveLocaleSelect
{
    public $activeLocale = null;

    public ?array $translatableLocales = null;

    protected function getActiveLocaleSelectAction(): SelectAction
    {
        return SelectAction::make('activeLocale')
            ->label(__('filament-spatie-laravel-translatable-plugin::actions.active_locale.label'))
            ->options(
                collect($this->getTranslatableLocales())
                    ->mapWithKeys(function (string $locale): array {
                        return [$locale => locale_get_display_name($locale, app()->getLocale())];
                    })
                    ->toArray(),
            );
    }

    public function setTranslatableLocales(array $locales): void
    {
        $this->translatableLocales = $locales;
    }

    protected function getTranslatableLocales(): array
    {
        return $this->translatableLocales ?? static::getResource()::getTranslatableLocales();
    }
}
