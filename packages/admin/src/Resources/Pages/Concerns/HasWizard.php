<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Forms\Components\Wizard;
use Filament\Resources\Form;

trait HasWizard
{
    protected function getBaseResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        return parent::getBaseResourceForm(
            columns: $columns,
            isDisabled: $isDisabled,
        )
            ->wizard()
            ->modifyBaseComponentUsing(function (Wizard $component) {
                $component->submit($this->getSubmitFormAction());
            });
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
