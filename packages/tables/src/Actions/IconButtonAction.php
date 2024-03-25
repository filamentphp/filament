<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Action;

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
