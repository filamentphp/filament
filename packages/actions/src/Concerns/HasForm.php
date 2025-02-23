<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait HasForm
{
    /**
     * @var array<string, mixed>
     */
    protected array $formData = [];

    protected ?Closure $mutateFormDataUsing = null;

    protected bool | Closure | null $hasFormWrapper = null;

    /**
     * @deprecated Use `disabledSchema() instead.
     */
    public function disableForm(bool | Closure $condition = true): static
    {
        $this->disabledSchema($condition);

        return $this;
    }

    /**
     * @deprecated Use `disabledSchema() instead.
     */
    public function disabledForm(bool | Closure $condition = true): static
    {
        $this->disabledSchema($condition);

        return $this;
    }

    /**
     * @deprecated Use `schema() instead.
     *
     * @param  array<Component| Action> | Closure | null  $form
     */
    public function form(array | Closure | null $form): static
    {
        $this->schema($form);

        return $this;
    }

    /**
     * @deprecated Use `getSchema()` instead.
     */
    public function getForm(Schema $schema): ?Schema
    {
        return $this->getSchema($schema);
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

    /**
     * @return array<string, mixed>
     */
    public function getRawFormData(): array
    {
        return $this->getLivewire()->mountedActions[$this->getNestingIndex()]['data'] ?? [];
    }

    /**
     * @deprecated Use `isSchemaDisabled()` instead.
     */
    public function isFormDisabled(): bool
    {
        return $this->isSchemaDisabled();
    }

    public function formWrapper(bool | Closure | null $condition = true): static
    {
        $this->hasFormWrapper = $condition;

        return $this;
    }

    public function hasFormWrapper(): bool
    {
        return (bool) ($this->evaluate($this->hasFormWrapper) ?? (! $this->isWizard()));
    }
}
