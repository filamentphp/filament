<?php

namespace Filament\Actions\Concerns;

use Filament\SpatieLaravelTranslatablePlugin;

trait HasTranslatableLocaleOptions
{
    public function setTranslatableLocaleOptions(): static
    {
        $this->options(function (): array {
            $livewire = $this->getLivewire();

            if (! method_exists($livewire, 'getTranslatableLocales')) {
                return [];
            }

            $locales = [];

            /** @var SpatieLaravelTranslatablePlugin $plugin */
            $plugin = filament('spatie-laravel-translatable');

            foreach ($livewire->getTranslatableLocales() as $locale) {
                $locales[$locale] = $plugin->getLocaleLabel($locale) ?? $locale;
            }

            return $locales;
        });

        return $this;
    }
}
