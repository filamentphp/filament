<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\Form;

trait UsesResourceForm
{
    protected ?Form $resourceForm = null;

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = static::getResource()::form(Form::make($this));
        }

        return $this->resourceForm;
    }
}
