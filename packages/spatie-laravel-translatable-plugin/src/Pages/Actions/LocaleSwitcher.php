<?php

namespace Filament\Pages\Actions;

use Filament\Facades\SpatieLaravelTranslatablePlugin;

class LocaleSwitcher extends SelectAction
{
    public static function getDefaultName(): ?string
    {
        return 'activeLocale';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-spatie-laravel-translatable-plugin::actions.active_locale.label'));

        $this->options(function (): array {
            $livewire = $this->getLivewire();

            if (! method_exists($livewire, 'getTranslatableLocales')) {
                return [];
            }

            $locales = [];

            foreach ($livewire->getTranslatableLocales() as $locale) {
                $locales[$locale] = SpatieLaravelTranslatablePlugin::getLocaleLabel($locale, app()->getLocale());
            }

            return $locales;
        });
    }
}
