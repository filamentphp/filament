<?php

namespace Filament\Tables\Actions;

class IconButtonAction extends Action
{
    use Concerns\HasIcon;

    protected string $view = 'tables::actions.icon-button-action';
}
