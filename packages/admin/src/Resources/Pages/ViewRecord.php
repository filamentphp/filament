<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Actions\SelectAction;

class ViewRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.view-record';

    public $record;

    public $data;

    public $activeFormLocale = null;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/view-record.breadcrumb');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->getRecord($record);

        abort_unless(static::getResource()::canView($this->record), 403);

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

    public function updatedActiveFormLocale(): void
    {
        $this->fillTranslatableForm();
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return array_merge(
            ($resource::isTranslatable() ? [$this->getActiveFormLocaleSelectAction()] : []),
            ($resource::canEdit($this->record) ? [$this->getEditButtonAction()] : []),
        );
    }

    protected function getActiveFormLocaleSelectAction(): SelectAction
    {
        return SelectAction::make('activeFormLocale')
            ->label(__('filament::resources/pages/view-record.actions.active_form_locale.label'))
            ->options(
                collect(static::getResource()::getTranslatableLocales())
                    ->mapWithKeys(function (string $locale): array {
                        return [$locale => $locale];
                    })
                    ->toArray(),
            );
    }

    protected function getEditButtonAction(): ButtonAction
    {
        return ButtonAction::make('edit')
            ->label(__('filament::resources/pages/view-record.actions.edit.label'))
            ->url(fn () => static::getResource()::getUrl('edit', ['record' => $this->record]));
    }

    protected function getTitle(): string
    {
        return static::$title ?? (($recordTitle = $this->getRecordTitle()) ? $recordTitle : parent::getTitle());
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->disabled()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }
}
