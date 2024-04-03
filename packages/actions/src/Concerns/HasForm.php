<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schema\ComponentContainer;
use Filament\Schema\Components\Component;

trait HasForm
{
    /**
     * @var array<string, mixed>
     */
    protected array $formData = [];

    protected ?Closure $mutateFormDataUsing = null;

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
     * @param  array<Component> | Closure | null  $form
     */
    public function form(array | Closure | null $form): static
    {
        $this->schema($form);

        return $this;
    }

    /**
     * @deprecated Use `getSchema()` instead.
     */
    public function getForm(ComponentContainer $form): ?ComponentContainer
    {
        return $this->getSchema($form);
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
     * @deprecated Use `isSchemaDisabled()` instead.
     */
    public function isFormDisabled(): bool
    {
        return $this->isSchemaDisabled();
    }
}
