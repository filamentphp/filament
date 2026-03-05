<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait HasSchema
{
    /**
     * @var array<Component | Action | ActionGroup> | Closure | null
     */
    protected array | Closure | null $schema = null;

    protected ?Closure $modifyFormFieldUsing = null;

    /**
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function schema(array | Closure | null $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @deprecated Use `schema()` instead.
     *
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function form(array | Closure | null $schema): static
    {
        $this->schema($schema);

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
    public function getSchemaComponents(): array
    {
        $schema = $this->evaluate($this->schema);

        if ($schema !== null) {
            if ($this->isHeaderFilter()) {
                foreach ($schema as $component) {
                    if ($component instanceof Field) {
                        $component->hiddenLabel();
                    }
                }
            }

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

        if ($this->isHeaderFilter() && $field instanceof Field) {
            $field->hiddenLabel();
        }

        return [$field];
    }

    /**
     * @deprecated Use `getSchemaComponents()` instead.
     *
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return $this->getSchemaComponents();
    }

    public function hasSchema(): bool
    {
        return $this->evaluate($this->schema) !== null;
    }

    public function getFormField(): ?Field
    {
        return null;
    }

    public function getSchema(): Schema
    {
        $livewire = $this->getLivewire();

        $form = $this->isHeaderFilter()
            ? $livewire->getTableHeaderFiltersForm() /** @phpstan-ignore method.notFound */
            : $livewire->getTableFiltersForm();

        return $form
            ->getComponent($this->getName())
            ->getChildSchema();
    }
}
