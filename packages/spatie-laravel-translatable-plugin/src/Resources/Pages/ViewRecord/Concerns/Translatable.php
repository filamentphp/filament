<?php

namespace Filament\Resources\Pages\ViewRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableRecordTitle;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableRecordTitle;

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        if ($this->activeLocale === null) {
            $this->setActiveLocale();
        }

        $data = $this->record->attributesToArray();

        foreach (static::getResource()::getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->record->getTranslation($attribute, $this->activeLocale);
        }

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function setActiveLocale(): void
    {
        $resource = static::getResource();

        $availableLocales = array_keys($this->record->getTranslations($resource::getTranslatableAttributes()[0]));
        $resourceLocales = $this->getTranslatableLocales();
        $defaultLocale = $resource::getDefaultTranslatableLocale();

        $this->activeLocale = in_array($defaultLocale, $availableLocales) ? $defaultLocale : array_intersect($availableLocales, $resourceLocales)[0] ?? $defaultLocale;
        $this->record->setLocale($this->activeLocale);
    }

    public function updatedActiveLocale(): void
    {
        $this->fillForm();
    }
}
