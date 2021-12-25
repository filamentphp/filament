<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\Form;

trait UsesResourceForm
{
    protected ?Form $resourceForm = null;

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            if (method_exists(static::class, 'customForm')) {
                $this->resourceForm = static::customForm(Form::make()->columns(2));
            } else {
                $this->resourceForm = static::getResource()::form(Form::make()->columns(2));
            }
        }

        return $this->resourceForm;
    }
}
