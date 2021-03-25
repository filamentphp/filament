<?php

namespace Filament\Resources\Forms;

trait HasForm
{
    use \Filament\Forms\HasForm;

    public function getActions()
    {
        return $this->actions();
    }

    public function getForm()
    {
        if ($this->form !== null) {
            return $this->form;
        }

        return $this->form = $this->form(
            Form::for($this),
        );
    }

    protected function actions()
    {
        return [];
    }

    protected function form(Form $form)
    {
        return $form;
    }
}
