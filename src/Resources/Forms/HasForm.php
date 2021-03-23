<?php

namespace Filament\Resources\Forms;

trait HasForm
{
    use \Filament\Forms\HasForm;

    protected function form(Form $form)
    {
        return $form;
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
}
