<?php

namespace Filament\Resources\Forms;

trait HasForm
{
    use \Filament\Forms\HasForm;

    public function getForm()
    {
        if ($this->form !== null) {
            return $this->form;
        }

        return $this->form = $this->form(
            Form::for($this),
        );
    }

    protected function form(Form $form)
    {
        return $form;
    }
}
