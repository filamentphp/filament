<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Attributes\ServerOnly;

trait HasWizard /** @phpstan-ignore trait.unused */
{
    #[ServerOnly]
    public function getStartStep(): int
    {
        return 1;
    }

    #[ServerOnly]
    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->columns(null)
            ->components([
                $this->getWizardComponent(),
            ]);
    }

    #[ServerOnly]
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

    #[ServerOnly]
    public function hasFormWrapper(): bool
    {
        return false;
    }

    #[ServerOnly]
    public function getFormContentComponent(): Component
    {
        return EmbeddedSchema::make('form');
    }

    #[ServerOnly]
    public function getSteps(): array
    {
        return [];
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }
}
