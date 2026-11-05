<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait HasForm
{
    /**
     * @var array<Component | Action | ActionGroup> | Closure | null
     */
    protected array | Closure | null $formSchema = null;

    protected ?Closure $modifyFormFieldUsing = null;

    /**
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function form(array | Closure | null $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    /**
     * @deprecated Use `form()` instead.
     *
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function schema(array | Closure | null $schema): static
    {
        $this->form($schema);

        return $this;
    }

    public function modifyFormFieldUsing(?Closure $callback): static
    {
        $this->modifyFormFieldUsing = $callback;

        return $this;
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        $schema = $this->evaluate($this->formSchema);

        if ($schema !== null) {
            return $schema;
        }

        $field = $this->getFormField();

        if ($field === null) {
            return [];
        }

        $field = $this->evaluate(
            $this->modifyFormFieldUsing,
            namedInjections: [
                'field' => $field,
            ],
            typedInjections: [
                Component::class => $field,
                Field::class => $field,
                $field::class => $field,
            ],
        ) ?? $field;

        return [$field];
    }

    /**
     * @deprecated Use `getFormSchema()` instead.
     *
     * @return array<Component | Action | ActionGroup>
     */
    public function getSchemaComponents(): array
    {
        return $this->getFormSchema();
    }

    public function hasForm(): bool
    {
        return $this->evaluate($this->formSchema) !== null;
    }

    /**
     * @deprecated Use `hasForm()` instead.
     */
    public function hasSchema(): bool
    {
        return $this->hasForm();
    }

    public function getFormField(): ?Field
    {
        return null;
    }

    public function getForm(): Schema
    {
        return $this->getLivewire()
            ->getTableFiltersForm()
            ->getComponent($this->getName())
            ->getChildSchema();
    }

    /**
     * @deprecated Use `getForm()` instead.
     */
    public function getSchema(): Schema
    {
        return $this->getForm();
    }
}
