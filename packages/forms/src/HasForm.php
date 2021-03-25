<?php

namespace Filament\Forms;

trait HasForm
{
    use Concerns\CanHandleSelectFields;
    use Concerns\CanManipulateProperties;
    use Concerns\CanUploadFiles;
    use Concerns\CanValidateInput;

    protected $form;

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
