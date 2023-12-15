<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Forms\Components\Actions\Action;

class ShowPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'showPassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-eye');

        $this->extraAttributes([
            'x-show' => '! isPasswordRevealed',
        ]);

        $this->livewireClickHandlerEnabled(false);
    }

    public function getAlpineClickHandler(): string
    {
        return 'isPasswordRevealed = true';
    }
}
