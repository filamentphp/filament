<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Concerns\HasFormActions;
use Filament\Resources\Form;

trait UsesResourceForm
{
    use HasFormActions;

    protected ?Form $resourceForm = null;

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = $this->form(Form::make()->columns(2));
        }

        return $this->resourceForm;
    }

    protected function form(Form $form): Form
    {
        return static::getResource()::form($form);
    }
}
