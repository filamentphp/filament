<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Concerns\HasFormActions;
use Filament\Resources\Form;

trait UsesResourceForm
{
    use HasFormActions;

    protected ?Form $resourceForm = null;

    protected function getResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = $this->form(
                Form::make()
                    ->columns($columns)
                    ->disabled($isDisabled),
            );
        }

        return $this->resourceForm;
    }

    protected function form(Form $form): Form
    {
        return static::getResource()::form($form);
    }
}
