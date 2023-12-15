<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Forms\Components\Actions\Action;

class HidePasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'hidePassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-eye-slash');

        $this->extraAttributes([
            'x-cloak' => true,
            'x-show' => 'isPasswordRevealed',
        ]);

        $this->livewireClickHandlerEnabled(false);
    }

    public function getAlpineClickHandler(): string
    {
        return 'isPasswordRevealed = false';
    }
}
