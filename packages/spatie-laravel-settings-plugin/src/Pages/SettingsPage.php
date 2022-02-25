<?php

namespace Filament\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Support\Str;

/**
 * @property ComponentContainer $form
 */
class SettingsPage extends Page
{
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

        $settings = app(static::getSettings());

        $this->form->fill($settings->toArray());

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $this->callHook('beforeSave');

        $settings = app(static::getSettings());

        $settings->fill($data);
        $settings->save();

        $this->callHook('afterSave');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        } else {
            $this->notify('success', $this->getSavedNotificationMessage());
        }
    }

    protected function getSavedNotificationMessage(): string
    {
        return __('filament-spatie-laravel-settings-plugin::pages/settings-page.messages.saved');
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    public static function getSettings(): string
    {
        return static::$settings ?? (string) Str::of(class_basename(static::class))
                ->beforeLast('Settings')
                ->prepend('App\\Settings\\')
                ->append('Settings');
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label(__('filament-spatie-laravel-settings-plugin::pages/settings-page.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->columns(2),
        ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
