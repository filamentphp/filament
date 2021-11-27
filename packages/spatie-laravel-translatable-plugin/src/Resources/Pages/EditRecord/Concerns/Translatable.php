<?php

namespace Filament\Resources\Pages\EditRecord\Concerns;

use Filament\Pages\Actions\SelectAction;
use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSelect;

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

        $this->activeFormLocale = array_intersect($availableLocales, $resourceLocales)[0] ?? $resource::getDefaultTranslatableLocale();
        $this->record->setLocale($this->activeFormLocale);
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $this->record->setLocale($this->activeFormLocale)->fill($data)->save();

        $this->callHook('afterSave');

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl);
        } else {
            $this->notify('success', __('filament::resources/pages/edit-record.messages.saved'));
        }
    }

    public function updatedActiveFormLocale(): void
    {
        $this->fillTranslatableForm();
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
