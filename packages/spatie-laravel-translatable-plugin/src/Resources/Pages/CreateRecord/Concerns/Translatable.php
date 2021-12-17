<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSelect;

trait Translatable
{
    use HasActiveFormLocaleSelect;

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->setActiveFormLocale();

        $this->fillForm();
    }

    protected function setActiveFormLocale(): void
    {
        $this->activeFormLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    public function create(bool $another = false): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $this->record = static::getModel()::usingLocale(
            $this->activeFormLocale,
        )->fill($data);

        $this->record->save();

        $this->form->model($this->record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($another) {
            $this->fillForm();

            $this->notify('success', __('filament::resources/pages/create-record.messages.created'));

            return;
        }

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    protected function getActions(): array
    {
        return array_merge(
            [$this->getActiveFormLocaleSelectAction()],
            parent::getActions() ?? [],
        );
    }
}
