<?php

namespace Filament\Tables\Actions;

/**
 * @deprecated Use `\Filament\Tables\Actions\Action` instead, with the `button()` method.
 * @see Action
 */
class ButtonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();
    }
}
