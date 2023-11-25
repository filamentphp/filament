<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;

trait Translatable
{
    use HasActiveLocaleSwitcher;

    public function mountTranslatable(): void
    {
        if (
            blank($this->activeLocale) ||
            (! in_array($this->activeLocale, $this->getTranslatableLocales()))
        ) {
            $this->setActiveLocale();
        }
    }

    public function getTranslatableLocales(): array
    {
        return filament('spatie-laravel-translatable')->getDefaultLocales();
    }

    public function getDefaultTranslatableLocale(): string
    {
        return $this->getTranslatableLocales()[0];
    }

    public function getActiveTableLocale(): ?string
    {
        return $this->activeLocale;
    }

    protected function setActiveLocale(): void
    {
        $this->activeLocale = $this->getDefaultTranslatableLocale();
    }
}
