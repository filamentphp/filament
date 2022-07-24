<?php

namespace Filament\Resources\Pages\ViewRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSwitcher;

trait Translatable
{
    use HasActiveFormLocaleSwitcher;

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        if ($this->activeFormLocale === null) {
            $this->setActiveFormLocale();
        }

        $data = $this->record->toArray();

        foreach (static::getResource()::getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->record->getTranslation($attribute, $this->activeFormLocale);
        }

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function setActiveFormLocale(): void
    {
        $resource = static::getResource();

        $availableLocales = array_keys($this->record->getTranslations($resource::getTranslatableAttributes()[0]));
        $resourceLocales = $this->getTranslatableLocales();
        $defaultLocale = $resource::getDefaultTranslatableLocale();

        $this->activeLocale = $this->activeFormLocale = in_array($defaultLocale, $availableLocales) ? $defaultLocale : array_intersect($availableLocales, $resourceLocales)[0] ?? $defaultLocale;
        $this->record->setLocale($this->activeFormLocale);
    }

    public function updatedActiveFormLocale(): void
    {
        $this->fillForm();
    }

    protected function getActions(): array
    {
        return array_merge(
            [$this->getActiveFormLocaleSelectAction()],
            parent::getActions(),
        );
    }
}
