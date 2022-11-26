<?php

namespace Filament\Tables\Actions\Modal\Actions;

use Filament\Actions\Modal\Actions\Action as BaseAction;

class Action extends BaseAction
{
    /**
     * @var view-string $view
     */
    protected string $view = 'filament-tables::actions.modal.actions.button-action';

    public function button(): static
    {
        $this->view('filament-tables::actions.modal.actions.button-action');

        return $this;
    }
}
