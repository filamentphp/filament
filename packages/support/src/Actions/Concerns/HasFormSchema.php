<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\Components\Wizard;

trait HasFormSchema
{
    protected array | Closure $formSchema = [];

    public function form(array | Closure $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): array
    {
        $schema = $this->evaluate($this->formSchema);

        if ($this->isWizard()) {
            return [
                Wizard::make($schema)
                    ->cancelAction($this->getModalCancelAction())
                    ->submitAction($this->getModalSubmitAction()),
            ];
        }

        return $schema;
    }

    public function hasFormSchema(): bool
    {
        return (bool) count($this->getFormSchema());
    }
}
