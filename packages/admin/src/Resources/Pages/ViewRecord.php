<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;
use Filament\View\Components\Actions\SelectAction;

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
        return static::$breadcrumb ?? 'View';
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
        $resource = static::getResource();

        if ($this->activeFormLocale === null) {
            $availableLocales = array_keys($this->record->getTranslations($resource::getTranslatableAttributes()[0]));
            $resourceLocales = $resource::getTranslatableLocales();

            $this->activeFormLocale = array_intersect($availableLocales, $resourceLocales)[0] ?? $resource::getDefaultTranslatableLocale();
            $this->record->setLocale($this->activeFormLocale);
        }

        $data = $this->record->toArray();

        foreach ($resource::getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->record->getTranslation($attribute, $this->activeFormLocale);
        }

        $this->form->fill($data);
    }

    public function updatedActiveFormLocale(): void
    {
        $this->fillTranslatableForm();
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
            ButtonAction::make('edit')
                ->label('Edit')
                ->url(fn () => $resource::getUrl('edit', ['record' => $this->record]))
                ->hidden(! $resource::canEdit($this->record)),
        ];
    }

    protected function getDynamicTitle(): string
    {
        return ($recordTitle = $this->getRecordTitle()) ? $recordTitle : static::getTitle();
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
