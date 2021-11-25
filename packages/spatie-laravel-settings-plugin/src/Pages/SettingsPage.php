<?php

namespace Filament\Pages;

use Filament\Forms;
use Filament\View\Components\Actions\ButtonAction;

class SettingsPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $settings;

    protected static string $view = 'filament-spatie-laravel-settings-plugin::pages.settings-page';

    public $data;

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $settings = app(static::$settings);

        $this->form->fill($settings->toArray());

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $settings = app(static::$settings);

        $settings->fill($data);
        $settings->save();

        $this->callHook('afterSave');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        } else {
            $this->notify('success', 'Saved!');
        }
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label('Save')
                ->submit(),
        ];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->columns(2),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
