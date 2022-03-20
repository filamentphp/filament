<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Actions\SelectAction;

trait HasActiveFormLocaleSelect
{
    public $activeFormLocale = null;

    public ?array $translatableLocales = null;

    protected function getActiveFormLocaleSelectAction(): SelectAction
    {
        return SelectAction::make('activeFormLocale')
            ->label(__('filament-spatie-laravel-translatable-plugin::actions.active_form_locale.label'))
            ->options(
                collect($this->getTranslatableLocales())
                    ->mapWithKeys(function (string $locale): array {
                        return [$locale => locale_get_display_name($locale, app()->getLocale())];
                    })
                    ->toArray(),
            );
    }

    protected function getRecordTitle(): ?string
    {
        if ($this->activeFormLocale) {
            $this->record->setLocale($this->activeFormLocale);
        }

        return parent::getRecordTitle();
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
