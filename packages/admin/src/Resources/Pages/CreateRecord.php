<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;
use Filament\View\Components\Actions\SelectAction;

class CreateRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.create-record';

    public $record;

    public $data;

    public $activeFormLocale = null;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Create';
    }

    public function mount(): void
    {
        static::authorizeResourceAccess();

        $resource = static::getResource();

        abort_unless($resource::canCreate(), 403);

        if ($resource::isTranslatable()) {
            $this->activeFormLocale = $resource::getDefaultTranslatableLocale();
        }

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function create(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeCreate');

        $resource = static::getResource();

        if ($resource::isTranslatable()) {
            $this->record = static::getModel()::usingLocale(
                $this->activeFormLocale,
            )->fill($data);
            $this->record->save();
        } else {
            $this->record = static::getModel()::create($data);
        }

        $this->form->model($this->record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
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
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('create')
                ->label('Create')
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
                ->model(static::getModel())
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        $resource = static::getResource();

        if ($resource::canView($this->record)) {
            return $resource::getUrl('view', ['record' => $this->record]);
        }

        if ($resource::canEdit($this->record)) {
            return $resource::getUrl('edit', ['record' => $this->record]);
        }

        return null;
    }
}
