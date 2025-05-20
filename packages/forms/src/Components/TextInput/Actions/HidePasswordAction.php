<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Support\Facades\FilamentIcon;

class HidePasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'hidePassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-forms::components.text_input.actions.hide_password.label'));

        $this->icon(FilamentIcon::resolve('forms::components.text-input.actions.hide-password') ?? 'heroicon-m-eye-slash');

        $this->defaultColor('gray');

        $this->extraAttributes([
            'x-cloak' => true,
            'x-show' => 'isPasswordRevealed',
        ], merge: true);

        $this->alpineClickHandler('isPasswordRevealed = false');
    }
}
