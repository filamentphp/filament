<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\SpatieLaravelTranslatableContentDriver;
use Filament\Support\Contracts\TranslatableContentDriver;

trait HasActiveLocaleSwitcher
{
    public $activeLocale = null;

    public ?array $translatableLocales = null;

    public function setTranslatableLocales(array $locales): void
    {
        $this->translatableLocales = $locales;
    }

    public function getTranslatableLocales(): array
    {
        return $this->translatableLocales ?? static::getResource()::getTranslatableLocales();
    }

    public function getActiveFormLocale(): ?string
    {
        return $this->activeLocale;
    }

    /**
     * @return class-string<TranslatableContentDriver> | null
     */
    public function getFormTranslatableContentDriver(): ?string
    {
        return SpatieLaravelTranslatableContentDriver::class;
    }
}
