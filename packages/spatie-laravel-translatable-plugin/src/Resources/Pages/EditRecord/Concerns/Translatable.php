<?php

namespace Filament\Resources\Pages\EditRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSelect;
use Illuminate\Database\Eloquent\Model;

trait Translatable
{
    use HasActiveFormLocaleSelect;

    public $activeFormLocale = null;

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

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function setActiveFormLocale(): void
    {
        $resource = static::getResource();

        $availableLocales = array_keys($this->record->getTranslations($resource::getTranslatableAttributes()[0]));
        $resourceLocales = $resource::getTranslatableLocales();
        $defaultLocale = $resource::getDefaultTranslatableLocale();

        $this->activeFormLocale = in_array($defaultLocale, $availableLocales) ? $defaultLocale : array_intersect($availableLocales, $resourceLocales)[0] ?? $defaultLocale;
        $this->record->setLocale($this->activeFormLocale);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $resource = static::getResource();
        $translatableFields = $resource::getTranslatableAttributes();

        collect($data)
            ->filter(fn ($value, $key) => in_array($key, $translatableFields))
            ->each(fn ($value, $key) => $record->setTranslation($key, $this->activeFormLocale, $value))
        ;

        collect($data)
            ->reject(fn ($value, $key) => in_array($key, $translatableFields))
            ->each(fn ($value, $key) => $record->setAttribute($key, $value))
        ;

        $record->save();

        return $record;
    }

    public function updatedActiveFormLocale(): void
    {
        $this->fillForm();
    }

    public function updatingActiveFormLocale(): void
    {
        $this->save(shouldRedirect: false);
    }

    protected function getActions(): array
    {
        return array_merge(
            [$this->getActiveFormLocaleSelectAction()],
            parent::getActions(),
        );
    }
}
