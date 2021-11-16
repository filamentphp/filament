<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;
use Filament\View\Components\Actions\SelectAction;

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
        return static::$breadcrumb ?? 'Edit';
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
            $this->notify('success', 'Saved!');
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

        return [
            SelectAction::make('activeFormLocale')
                ->label('Locale')
                ->options(
                    collect($resource::getTranslatableLocales())
                        ->mapWithKeys(function (string $locale): array {
                            return [$locale => $locale];
                        })
                        ->toArray(),
                )
                ->hidden(! $resource::isTranslatable()),
            ButtonAction::make('view')
                ->label('View')
                ->url(fn () => $resource::getUrl('view', ['record' => $this->record]))
                ->color('secondary')
                ->hidden(! $resource::canView($this->record)),
            ButtonAction::make('delete')
                ->label('Delete')
                ->action('openDeleteModal')
                ->color('danger')
                ->hidden(! $resource::canDelete($this->record)),
        ];
    }

    protected function getTitle(): string
    {
        return static::$title ?? (($recordTitle = $this->getRecordTitle()) ? "Edit {$recordTitle}" : parent::getTitle());
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label('Save')
                ->submit(),
            ButtonAction::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl())
                ->color('secondary'),
        ];
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
