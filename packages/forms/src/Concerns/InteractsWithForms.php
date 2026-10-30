<?php

namespace Filament\Forms\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait InteractsWithForms /** @phpstan-ignore trait.unused */
{
    use InteractsWithSchemas {
        getCachedSchemas as getBaseCachedSchemas;
    }

    protected bool $hasCachedForms = false;

    /**
     * @return array<string, Schema>
     */
    #[Unexposed]
    public function getCachedSchemas(): array
    {
        if (! $this->hasCachedForms) {
            $this->cacheForms();
        }

        return $this->getBaseCachedSchemas();
    }

    /**
     * @deprecated You do not need to register forms in the `getForms()` method any longer. Define a method of the form's name and return the form from it.
     *
     * @return array<string, Schema>
     */
    protected function cacheForms(): array
    {
        $this->isCachingSchemas = true;

        $this->cachedSchemas = [
            ...$this->cachedSchemas,
            ...collect($this->getForms())
                ->merge($this->getTraitForms())
                ->mapWithKeys(function (Schema | string | null $form, string | int $formName): array {
                    if ($form === null) {
                        return ['' => null];
                    }

                    if (is_string($formName)) {
                        return [$formName => $form];
                    }

                    if (! method_exists($this, $form)) {
                        $livewireClass = $this::class;

                        throw new LogicException("Form configuration method [{$form}()] is missing from Livewire component [{$livewireClass}].");
                    }

                    return [$form => $this->{$form}($this->makeSchema())];
                })
                ->forget('')
                ->map(fn (Schema $schema, string $formName) => $schema->key($formName))
                ->all(),
        ];

        $this->isCachingSchemas = false;
        $this->hasCachedForms = true;

        return $this->cachedSchemas;
    }

    /**
     * @deprecated You do not need to register forms in the `getForms()` method any longer. Define a method of the form's name and return the form from it.
     *
     * @return array<int | string, string | Schema>
     */
    #[Unexposed]
    public function getTraitForms(): array
    {
        $forms = [];

        foreach (class_uses_recursive($class = static::class) as $trait) {
            if (method_exists($class, $method = 'get' . class_basename($trait) . 'Forms')) {
                $forms = [
                    ...$forms,
                    ...$this->{$method}(),
                ];
            }
        }

        return $forms;
    }

    /**
     * @deprecated You do not need to register forms in the `getForms()` method any longer. Define a method of the form's name and return the form from it.
     *
     * @return array<int | string, string | Schema>
     */
    protected function getForms(): array
    {
        return [
            'form',
        ];
    }

    #[Unexposed]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->model($this->getFormModel())
            ->statePath($this->getFormStatePath())
            ->operation($this->getFormContext());
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     *
     * @return Model|class-string<Model>|null
     */
    protected function getFormModel(): Model | string | null
    {
        return null;
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     *
     * @return array<Component | Action | ActionGroup>
     */
    protected function getFormSchema(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     */
    protected function getFormContext(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     */
    protected function getFormStatePath(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use `callMountedAction()` instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    #[Exposed]
    public function callMountedFormComponentAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use `mountAction()` instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    #[Exposed]
    public function mountFormComponentAction(string $component, string $name, array $arguments = []): mixed
    {
        return $this->mountAction($name, $arguments, context: [
            'schemaComponent' => $component,
        ]);
    }

    /**
     * @deprecated Use `mountedActionShouldOpenModal()` instead.
     */
    #[Unexposed]
    public function mountedFormComponentActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedAction);
    }

    /**
     * @deprecated Use `mountedActionHasSchema()` instead.
     */
    #[Unexposed]
    public function mountedFormComponentActionHasForm(?Action $mountedAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedAction);
    }

    /**
     * @deprecated Use `getMountedAction()` instead.
     */
    #[Unexposed]
    public function getMountedFormComponentAction(?int $actionNestingIndex = null): ?Action
    {
        return $this->getMountedAction($actionNestingIndex);
    }

    /**
     * @deprecated Use `unmountAction()` instead.
     */
    #[Exposed]
    public function unmountFormComponentAction(bool $shouldCancelParentActions = true): void
    {
        $this->unmountAction(cancelParentActions: $shouldCancelParentActions ? null : false);
    }
}
