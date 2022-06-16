<?php

namespace Filament\Pages\Actions;

class LocaleSwitcher extends SelectAction
{
    public static function make(string $name = 'activeLocale'): static
    {
        return parent::make($name);
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

            return collect($livewire->getTranslatableLocales())
                ->mapWithKeys(function (string $locale): array {
                    return [$locale => locale_get_display_name($locale, app()->getLocale())];
                })
                ->toArray();
        });
    }
}
