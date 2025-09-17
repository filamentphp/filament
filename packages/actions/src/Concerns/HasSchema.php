<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

trait HasSchema
{
    /**
     * @var array<Component | Action | ActionGroup> | Closure | null
     */
    protected array | Closure | null $schema = null;

    protected bool | Closure $isSchemaDisabled = false;

    protected bool | Closure | null $hasFormWrapper = null;

    /**
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function schema(array | Closure | null $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function disabledSchema(bool | Closure $condition = true): static
    {
        $this->isSchemaDisabled = $condition;

        return $this;
    }

    public function isSchemaDisabled(): bool
    {
        return (bool) $this->evaluate($this->isSchemaDisabled);
    }

    public function getSchema(Schema $schema): ?Schema
    {
        $modifiedSchema = $this->evaluate($this->schema ?? $this->getHasActionsLivewire()?->getDefaultActionSchemaResolver($this), [
            'form' => $schema,
            'schema' => $schema,
            'infolist' => $schema,
        ]);

        if ($modifiedSchema === null) {
            return null;
        }

        if (is_array($modifiedSchema) && (! count($modifiedSchema))) {
            return null;
        }

        if (is_array($modifiedSchema) && $this->isWizard()) {
            $wizard = Wizard::make($modifiedSchema)
                ->contained(false)
                ->startOnStep($this->getWizardStartStep())
                ->cancelAction($this->getModalCancelAction())
                ->submitAction($this->getModalSubmitAction())
                ->alpineSubmitHandler("\$wire.{$this->getLivewireCallMountedActionName()}()")
                ->skippable($this->isWizardSkippable())
                ->disabled($this->isSchemaDisabled());

            if ($this->modifyWizardUsing) {
                $wizard = $this->evaluate($this->modifyWizardUsing, [
                    'wizard' => $wizard,
                ]) ?? $wizard;
            }

            $modifiedSchema = [$wizard];
        }

        if (is_array($modifiedSchema)) {
            $modifiedSchema = $schema->components($modifiedSchema);
        }

        if ($this->isSchemaDisabled()) {
            return $modifiedSchema->disabled();
        }

        return $modifiedSchema;
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

    /**
     * @deprecated Use `isSchemaDisabled()` instead.
     */
    public function isFormDisabled(): bool
    {
        return $this->isSchemaDisabled();
    }
}
