<?php

namespace Filament\Resources\Forms;

trait HasForm
{
    use \Filament\Forms\HasForm;

    protected function form()
    {
        return Form::for($this);
    }
}
