<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
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

        $this->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_HIDE_PASSWORD) ?? Heroicon::EyeSlash);

        $this->defaultColor('gray');

        $this->extraAttributes([
            'x-cloak' => 'x-cloak',
            'x-show' => 'isPasswordRevealed',
        ], merge: true);

        $this->alpineClickHandler('isPasswordRevealed = false');
    }
}
