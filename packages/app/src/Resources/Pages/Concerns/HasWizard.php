<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;

trait HasWizard
{
    public function getStartStep(): int
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

    public function getFormActions(): array
    {
        return [];
    }

    public function getSteps(): array
    {
        return [];
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }
}
