<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schema\Components\Wizard;
use Filament\Schema\Schema;

trait HasWizard
{
    public function getStartStep(): int
    {
        return 1;
    }

    public function form(Schema $form): Schema
    {
        return parent::form($form)
            ->schema([
                Wizard::make($this->getSteps())
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction($this->getSubmitFormAction())
                    ->skippable($this->hasSkippableSteps()),
            ])
            ->columns(null);
    }

    /**
     * @return array<Action | ActionGroup>
     */
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
