<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;

trait HasForm
{
    protected array $formData = [];

    protected array | Closure $formSchema = [];

    protected bool | Closure $isFormDisabled = false;

    protected ?Closure $mutateFormDataUsing = null;

    public function disableForm(bool | Closure $condition = true): static
    {
        $this->isFormDisabled = $condition;

        return $this;
    }

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
                    ->startOnStep($this->getWizardStartStep())
                    ->cancelAction($this->getModalCancelAction())
                    ->submitAction($this->getModalSubmitAction())
                    ->skippable($this->isWizardSkippable())
                    ->disabled($this->isFormDisabled()),
            ];
        } elseif ($this->isFormDisabled()) {
            return [
                Group::make($schema)->disabled(),
            ];
        }

        return $schema;
    }

    public function hasForm(): bool
    {
        return $this->hasFormSchema();
    }

    public function hasFormSchema(): bool
    {
        return (bool) count($this->getFormSchema());
    }

    public function mutateFormDataUsing(?Closure $callback): static
    {
        $this->mutateFormDataUsing = $callback;

        return $this;
    }

    public function formData(array $data, bool $shouldMutate = true): static
    {
        if ($shouldMutate && $this->mutateFormDataUsing) {
            $data = $this->evaluate($this->mutateFormDataUsing, [
                'data' => $data,
            ]);
        }

        $this->formData = $data;

        return $this;
    }

    public function resetFormData(): static
    {
        $this->formData([], shouldMutate: false);

        return $this;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function isFormDisabled(): bool
    {
        return $this->evaluate($this->isFormDisabled);
    }
}
