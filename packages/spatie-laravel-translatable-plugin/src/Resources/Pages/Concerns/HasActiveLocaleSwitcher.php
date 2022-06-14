<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\LocaleSwitcher;

trait HasActiveLocaleSwitcher
{
    public $activeLocale = null;

    public ?array $translatableLocales = null;

    protected function getActiveLocaleSelectAction(): Action
    {
        return LocaleSwitcher::make();
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
