<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

trait HasWizard /** @phpstan-ignore trait.unused */
{
    public function getStartStep(): int
    {
        return 1;
    }

    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)
            ->columns(null);
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->components([
                $this->getWizardComponent(),
            ]);
    }

    public function getWizardComponent(): Component
    {
        return Wizard::make($this->getSteps())
            ->startOnStep($this->getStartStep())
            ->cancelAction($this->getCancelFormAction())
            ->submitAction($this->getSubmitFormAction())
            ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
            ->skippable($this->hasSkippableSteps())
            ->contained(false);
    }

    public function hasFormWrapper(): bool
    {
        return false;
    }

    public function getFormContentComponent(): Component
    {
        return EmbeddedSchema::make('form');
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
