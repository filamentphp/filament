<?php

namespace Filament\Forms;

trait HasForm
{
    use Concerns\CanHandleSelectFields;
    use Concerns\CanManipulateProperties;
    use Concerns\CanUploadFiles;
    use Concerns\CanValidateInput;

    public function getForm()
    {
        return $this->form(
            Form::for($this),
        );
    }

    protected function form(Form $form)
    {
        return $form;
    }
}
