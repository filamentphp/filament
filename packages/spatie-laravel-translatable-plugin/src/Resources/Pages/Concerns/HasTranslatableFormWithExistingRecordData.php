<?php

namespace Filament\Resources\Pages\Concerns;

trait HasTranslatableFormWithExistingRecordData
{
    protected function fillForm(): void
    {
        $originalActiveLocale = $this->activeLocale;

        foreach ($this->getTranslatableLocales() as $locale) {
            $this->setActiveLocale($locale);

            $data = $this->getRecord()->attributesToArray();

            foreach (static::getResource()::getTranslatableAttributes() as $attribute) {
                $data[$attribute] = $this->getRecord()->getTranslation($attribute, $this->activeLocale);
            }

            /** @internal Read the DocBlock above the following method. */
            $this->fillFormWithDataAndCallHooks($data);
        }

        $this->setActiveLocale($originalActiveLocale);
    }

    protected function setActiveLocale(?string $locale = null): void
    {
        $this->activeLocale = filled($locale) ? $locale : $this->getDefaultTranslatableLocale();
        $this->cacheForm('form', $this->getForms()['form']);
    }

    protected function getDefaultTranslatableLocale(): string
    {
        $resource = static::getResource();

        $availableLocales = array_keys($this->getRecord()->getTranslations($resource::getTranslatableAttributes()[0]));
        $defaultLocale = $resource::getDefaultTranslatableLocale();

        if (in_array($defaultLocale, $availableLocales)) {
            return $defaultLocale;
        }

        $resourceLocales = $this->getTranslatableLocales();

        return array_intersect($availableLocales, $resourceLocales)[0] ?? $defaultLocale;
    }

    public function getFormStatePath(): ?string
    {
        return "data.{$this->activeLocale}";
    }
}
