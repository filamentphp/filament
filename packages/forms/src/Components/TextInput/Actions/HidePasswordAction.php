<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Actions\Action;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

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

        $this->icon(FilamentIcon::resolve('forms::components.text-input.actions.hide-password') ?? Heroicon::EyeSlash);

        $this->color('gray');

        $this->extraAttributes([
            'x-cloak' => 'x-cloak',
            'x-show' => 'isPasswordRevealed',
        ], merge: true);

        $this->alpineClickHandler('isPasswordRevealed = false');
    }
}
