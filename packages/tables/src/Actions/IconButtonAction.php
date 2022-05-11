<?php

namespace Filament\Tables\Actions;

/**
 * @deprecated Use `\Filament\Tables\Actions\Action` instead, with the `iconButton()` method.
 * @see Action
 */
class IconButtonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }
}
