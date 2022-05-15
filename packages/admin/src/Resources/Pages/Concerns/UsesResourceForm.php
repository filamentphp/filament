<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Concerns\HasFormActions;
use Filament\Resources\Form;

trait UsesResourceForm
{
    use HasFormActions;

    protected function getResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        return $this->form(
            $this->getBaseResourceForm(
                columns: $columns,
                isDisabled: $isDisabled,
            ),
        );
    }

    protected function getBaseResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        return Form::make()
            ->columns($columns)
            ->disabled($isDisabled);
    }

    protected function form(Form $form): Form
    {
        return static::getResource()::form($form);
    }
}
