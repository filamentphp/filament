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
                $component
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction($this->getSubmitFormAction());
            });
    }

    protected function getStartStep(): int
    {
        return 1;
    }

    protected function form(Form $form): Form
    {
        return $form->schema($this->getSteps());
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSteps(): array
    {
        return [];
    }
}
