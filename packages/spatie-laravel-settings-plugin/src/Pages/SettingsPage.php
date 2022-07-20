<?php

namespace Filament\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
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

        $data = $this->mutateFormDataBeforeFill($settings->toArray());

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $settings = app(static::getSettings());

        $settings->fill($data);
        $settings->save();

        $this->callHook('afterSave');

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }

        if (filled($this->getSavedNotificationMessage())) {
            $this->notify(
                Notification::make()
                    ->title($this->getSavedNotificationMessage())
                    ->success(),
            );
        }
    }

    protected function getSavedNotificationMessage(): ?string
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
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
            Action::make('save')
                ->label(__('filament-spatie-laravel-settings-plugin::pages/settings-page.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->columns(2)
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
