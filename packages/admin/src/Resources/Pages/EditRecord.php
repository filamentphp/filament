<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Actions\SelectAction;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    public $activeFormLocale = null;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/edit-record.breadcrumb');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->getRecord($record);

        abort_unless(static::getResource()::canEdit($this->record), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        if (static::getResource()::isTranslatable()) {
            $this->fillTranslatableForm();
        } else {
            $this->form->fill($this->record->toArray());
        }

        $this->callHook('afterFill');
    }

    protected function fillTranslatableForm(): void
    {
        if ($this->activeFormLocale === null) {
            $this->setActiveFormLocale();
        }

        $data = $this->record->toArray();

        foreach (static::getResource()::getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->record->getTranslation($attribute, $this->activeFormLocale);
        }

        $this->form->fill($data);
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

        $resource = static::getResource();

        if ($resource::isTranslatable()) {
            $this->record->setLocale($this->activeFormLocale)->fill($data)->save();
        } else {
            $this->record->update($data);
        }

        $this->callHook('afterSave');

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl);
        } else {
            $this->notify('success', __('filament::resources/pages/edit-record.messages.saved'));
        }
    }

    public function openDeleteModal(): void
    {
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'delete',
        ]);
    }

    public function delete(): void
    {
        abort_unless(static::getResource()::canDelete($this->record), 403);

        $this->record->delete();

        $this->redirect(static::getResource()::getUrl('index'));
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
        $resource = static::getResource();

        return array_merge(
            ($resource::isTranslatable() ? [$this->getActiveFormLocaleSelectAction()] : []),
            ($resource::canView($this->record) ? [$this->getViewButtonAction()] : []),
            ($resource::canDelete($this->record) ? [$this->getDeleteButtonAction()] : []),
        );
    }

    protected function getActiveFormLocaleSelectAction(): SelectAction
    {
        return SelectAction::make('activeFormLocale')
            ->label(__('filament::resources/pages/edit-record.actions.active_form_locale.label'))
            ->options(
                collect(static::getResource()::getTranslatableLocales())
                    ->mapWithKeys(function (string $locale): array {
                        return [$locale => $locale];
                    })
                    ->toArray(),
            );
    }

    protected function getViewButtonAction(): ButtonAction
    {
        return ButtonAction::make('view')
            ->label(__('filament::resources/pages/edit-record.actions.view.label'))
            ->url(fn () => static::getResource()::getUrl('view', ['record' => $this->record]))
            ->color('secondary');
    }

    protected function getDeleteButtonAction(): ButtonAction
    {
        return ButtonAction::make('delete')
            ->label(__('filament::resources/pages/edit-record.actions.delete.label'))
            ->action('openDeleteModal')
            ->color('danger');
    }

    protected function getTitle(): string
    {
        return static::$title ?? (($recordTitle = $this->getRecordTitle()) ? __('filament::resources/pages/edit-record.title', ['record' => $recordTitle]) : parent::getTitle());
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveButtonFormAction(),
            $this->getCancelButtonFormAction(),
        ];
    }

    protected function getSaveButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('save')
            ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
            ->submit();
    }

    protected function getCancelButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('cancel')
            ->label(__('filament::resources/pages/edit-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        $resource = static::getResource();

        if (! $resource::canView($this->record)) {
            return null;
        }

        return $resource::getUrl('view', ['record' => $this->record]);
    }
}
