<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
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

        $this->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_SHOW_PASSWORD) ?? Heroicon::Eye);

        $this->defaultColor('gray');

        $this->extraAttributes([
            'x-show' => '! isPasswordRevealed',
        ], merge: true);

        $this->alpineClickHandler('isPasswordRevealed = true');
    }
}
