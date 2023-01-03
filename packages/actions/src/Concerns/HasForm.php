<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;

trait HasForm
{
    /**
     * @var array<string, mixed>
     */
    protected array $formData = [];

    /**
     * @var array<Component> | Closure | null
     */
    protected array | Closure | null $form = null;

    protected bool | Closure $isFormDisabled = false;

    protected ?Closure $mutateFormDataUsing = null;

    /**
     * @deprecated Use `disabledForm() instead.
     */
    public function disableForm(bool | Closure $condition = true): static
    {
        $this->disabledForm($condition);

        return $this;
    }

    public function disabledForm(bool | Closure $condition = true): static
    {
        $this->isFormDisabled = $condition;

        return $this;
    }

    /**
     * @param  array<Component> | Closure | null  $form
     */
    public function form(array | Closure | null $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function getForm(Form $form): ?Form
    {
        $modifiedForm = $this->evaluate($this->form, [
            'form' => $form,
        ]);

        if ($modifiedForm === null) {
            return null;
        }

        if (is_array($modifiedForm) && (! count($modifiedForm))) {
            return null;
        }

        if (is_array($modifiedForm) && $this->isWizard()) {
            $modifiedForm = [
                Wizard::make($modifiedForm)
                    ->startOnStep($this->getWizardStartStep())
                    ->cancelAction($this->getModalCancelAction())
                    ->submitAction($this->getModalSubmitAction())
                    ->skippable($this->isWizardSkippable())
                    ->disabled($this->isFormDisabled()),
            ];
        }

        if (is_array($modifiedForm)) {
            $modifiedForm = $form->schema($modifiedForm);
        }

        if ($this->isFormDisabled()) {
            return $modifiedForm->disabled();
        }

        return $modifiedForm;
    }

    public function mutateFormDataUsing(?Closure $callback): static
    {
        $this->mutateFormDataUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    public function isFormDisabled(): bool
    {
        return (bool) $this->evaluate($this->isFormDisabled);
    }
}
