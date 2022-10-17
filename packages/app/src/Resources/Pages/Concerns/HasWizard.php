<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;

trait HasWizard
{
    protected function getStartStep(): int
    {
        return 1;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make($this->getSteps())
                ->startOnStep($this->getStartStep())
                ->cancelAction($this->getCancelFormAction())
                ->submitAction($this->getSubmitFormAction())
                ->skippable($this->hasSkippableSteps()),
        ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSteps(): array
    {
        return [];
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }
}
