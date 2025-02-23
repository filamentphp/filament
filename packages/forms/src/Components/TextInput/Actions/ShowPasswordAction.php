<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Actions\Action;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class ShowPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'showPassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-forms::components.text_input.actions.show_password.label'));

        $this->icon(FilamentIcon::resolve('forms::components.text-input.actions.show-password') ?? Heroicon::Eye);

        $this->color('gray');

        $this->extraAttributes([
            'x-show' => '! isPasswordRevealed',
        ], merge: true);

        $this->alpineClickHandler('isPasswordRevealed = true');
    }
}
