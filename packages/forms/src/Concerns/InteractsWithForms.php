<?php

namespace Filament\Forms\Concerns;

use Closure;
use Exception;
use Filament\Forms\Form;
use Filament\Schema\ComponentContainer;
use Filament\Schema\Components\Component;
use Filament\Schema\Concerns\InteractsWithSchemas;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait InteractsWithForms
{
    use InteractsWithSchemas {
        getCachedSchemas as baseGetCachedSchemas;
        makeSchema as baseMakeSchema;
    }

    protected bool $hasFormsModalRendered = false;

    protected bool $hasCachedForms = false;

    protected function cacheForm(string $name, Form | Closure | null $form): ?ComponentContainer
    {
        return $this->cacheSchema($name, $form);
    }

    /**
     * @return array<string, ComponentContainer>
     */
    public function getCachedSchemas(): array
    {
        if (! $this->hasCachedForms) {
            $this->cacheForms();
        }

        return $this->baseGetCachedSchemas();
    }

    /**
     * @return array<string, ComponentContainer>
     */
    protected function cacheForms(): array
    {
        $this->isCachingSchemas = true;

        $this->cachedSchemas = [
            ...$this->cachedSchemas,
            ...collect($this->getForms())
                ->merge($this->getTraitForms())
                ->mapWithKeys(function (ComponentContainer | string | null $form, string | int $formName): array {
                    if ($form === null) {
                        return ['' => null];
                    }

                    if (is_string($formName)) {
                        return [$formName => $form];
                    }

                    if (! method_exists($this, $form)) {
                        $livewireClass = $this::class;

                        throw new Exception("Form configuration method [{$form}()] is missing from Livewire component [{$livewireClass}].");
                    }

                    return [$form => $this->{$form}($this->makeForm()->key($form))];
                })
                ->forget('')
                ->all(),
        ];

        $this->isCachingSchemas = false;
        $this->hasCachedForms = true;

        foreach ($this->mountedFormComponentActions as $actionNestingIndex => $action) {
            $this->cacheSchema(
                "mountedFormComponentActionForm{$actionNestingIndex}",
                $this->getMountedFormComponentActionForm($actionNestingIndex),
            );
        }

        return $this->cachedSchemas;
    }

    /**
     * @return array<int | string, string | Form>
     */
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

    protected function hasCachedForm(string $name): bool
    {
        return $this->hasCachedSchema($name);
    }

    public function getForm(string $name): ?ComponentContainer
    {
        return $this->getSchema($name);
    }

    /**
     * @return array<string, ComponentContainer>
     */
    public function getCachedForms(): array
    {
        return $this->getCachedSchemas();
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form',
        ];
    }

    public function form(ComponentContainer $form): ComponentContainer
    {
        return $form
            ->schema($this->getFormSchema())
            ->model($this->getFormModel())
            ->statePath($this->getFormStatePath())
            ->operation($this->getFormContext());
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     */
    protected function getFormModel(): Model | string | null
    {
        return null;
    }

    /**
     * @deprecated Override the `form()` method to configure the default form.
     *
     * @return array<Component>
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

    protected function makeForm(): ComponentContainer
    {
        return Form::make($this);
    }

    /**
     * @param  class-string<ComponentContainer>  $type
     */
    protected function makeSchema(string $type): ComponentContainer
    {
        if (is_a($type, Form::class, allow_string: true)) {
            return $this->makeForm();
        }

        return $this->baseMakeSchema($type);
    }

    public function isCachingForms(): bool
    {
        return $this->isCachingSchemas();
    }

    public function getFormComponentFileAttachment(string $statePath): ?TemporaryUploadedFile
    {
        return $this->getSchemaComponentFileAttachment($statePath);
    }

    public function getActiveFormsLocale(): ?string
    {
        return $this->getActiveSchemaLocale();
    }

    public function getOldFormState(string $statePath): mixed
    {
        return $this->getOldSchemaState($statePath);
    }
}
