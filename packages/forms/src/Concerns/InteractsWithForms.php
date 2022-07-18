<?php

namespace Filament\Forms\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

trait InteractsWithForms
{
    use WithFileUploads;
    use HasFormComponentActions;

    public array $componentFileAttachments = [];

    protected ?array $cachedForms = null;

    protected bool $hasCachedForms = false;

    protected bool $isCachingForms = false;

    protected bool $hasModalViewRendered = false;

    public function __get($property)
    {
        if ((! $this->isCachingForms) && $form = $this->getCachedForm($property)) {
            return $form;
        }

        if ($property === 'modal') {
            return $this->getModalViewOnce();
        }

        return parent::__get($property);
    }

    protected function getModalViewOnce(): ?View
    {
        if ($this->hasModalViewRendered) {
            return null;
        }

        try {
            return view('forms::components.actions.modal.index');
        } finally {
            $this->hasModalViewRendered = true;
        }
    }

    public function dispatchFormEvent(...$args): void
    {
        foreach ($this->getCachedForms() as $form) {
            $form->dispatchEvent(...$args);
        }
    }

    public function getComponentFileAttachment(string $statePath): ?TemporaryUploadedFile
    {
        return data_get($this->componentFileAttachments, $statePath);
    }

    public function getComponentFileAttachmentUrl(string $statePath): ?string
    {
        foreach ($this->getCachedForms() as $form) {
            if ($url = $form->getComponentFileAttachmentUrl($statePath)) {
                return $url;
            }
        }

        return null;
    }

    public function getSelectOptionLabels(string $statePath): array
    {
        foreach ($this->getCachedForms() as $form) {
            if ($labels = $form->getSelectOptionLabels($statePath)) {
                return $labels;
            }
        }

        return [];
    }

    public function getSelectOptionLabel(string $statePath): ?string
    {
        foreach ($this->getCachedForms() as $form) {
            if ($label = $form->getSelectOptionLabel($statePath)) {
                return $label;
            }
        }

        return null;
    }

    public function getSelectOptions(string $statePath): array
    {
        foreach ($this->getCachedForms() as $form) {
            if ($results = $form->getSelectOptions($statePath)) {
                return $results;
            }
        }

        return [];
    }

    public function getSelectSearchResults(string $statePath, string $search): array
    {
        foreach ($this->getCachedForms() as $form) {
            if ($results = $form->getSelectSearchResults($statePath, $search)) {
                return $results;
            }
        }

        return [];
    }

    public function deleteUploadedFile(string $statePath, string $fileKey): void
    {
        foreach ($this->getCachedForms() as $form) {
            $form->deleteUploadedFile($statePath, $fileKey);
        }
    }

    public function getUploadedFileUrls(string $statePath): ?array
    {
        foreach ($this->getCachedForms() as $form) {
            if ($url = $form->getUploadedFileUrls($statePath)) {
                return $url;
            }
        }

        return null;
    }

    public function removeUploadedFile(string $statePath, string $fileKey): void
    {
        foreach ($this->getCachedForms() as $form) {
            $form->removeUploadedFile($statePath, $fileKey);
        }
    }

    public function reorderUploadedFiles(string $statePath, array $fileKeys): void
    {
        foreach ($this->getCachedForms() as $form) {
            $form->reorderUploadedFiles($statePath, $fileKeys);
        }
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $this->onValidationError($exception);

            $this->focusConcealedComponents(array_keys($exception->validator->failed()));

            throw $exception;
        }
    }

    protected function onValidationError(ValidationException $exception): void
    {
    }

    public function validateOnly($field, $rules = null, $messages = [], $attributes = [])
    {
        try {
            return parent::validateOnly($field, $rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $this->focusConcealedComponents(array_keys($exception->validator->failed()));

            throw $exception;
        }
    }

    public function getActiveFormLocale(): ?string
    {
        return null;
    }

    protected function callBeforeAndAfterSyncHooks($name, $value, $callback): void
    {
        parent::callBeforeAndAfterSyncHooks($name, $value, $callback);

        foreach ($this->getCachedForms() as $form) {
            $form->callAfterStateUpdated($name);
        }
    }

    protected function cacheForm(string $name, ComponentContainer | Closure | null $form): ?ComponentContainer
    {
        $this->isCachingForms = true;

        $form = value($form);

        if ($form) {
            $this->cachedForms[$name] = $form;
        }

        $this->isCachingForms = false;

        return $form;
    }

    protected function cacheForms(): array
    {
        $this->isCachingForms = true;

        $this->cachedForms = array_filter($this->getUncachedForms());

        $this->isCachingForms = false;

        $this->hasCachedForms = true;

        $this->cacheForm(
            'mountedFormComponentActionForm',
            $this->getMountedFormComponentActionForm(),
        );

        return $this->cachedForms;
    }

    protected function getUncachedForms(): array
    {
        return array_merge($this->getTraitForms(), $this->getForms());
    }

    protected function getTraitForms(): array
    {
        $forms = [];

        foreach (class_uses_recursive($class = static::class) as $trait) {
            if (method_exists($class, $method = 'get' . class_basename($trait) . 'Forms')) {
                $forms = array_merge($forms, $this->{$method}());
            }
        }

        return $forms;
    }

    protected function hasCachedForm($name): bool
    {
        return array_key_exists($name, $this->getCachedForms());
    }

    protected function getCachedForm($name): ?ComponentContainer
    {
        return $this->getCachedForms()[$name] ?? null;
    }

    protected function getCachedForms(): array
    {
        if (! $this->hasCachedForms) {
            return $this->cacheForms();
        }

        return $this->cachedForms;
    }

    protected function focusConcealedComponents(array $statePaths): void
    {
        $componentToFocus = null;

        foreach ($this->getCachedForms() as $form) {
            if ($componentToFocus = $form->getInvalidComponentToFocus($statePaths)) {
                break;
            }
        }

        if ($concealingComponent = $componentToFocus?->getConcealingComponent()) {
            $this->dispatchBrowserEvent('expand-concealing-component', [
                'id' => $concealingComponent->getId(),
            ]);
        }
    }

    protected function getFormModel(): Model | string | null
    {
        return null;
    }

    protected function getFormSchema(): array
    {
        return [];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->model($this->getFormModel())
                ->statePath($this->getFormStatePath()),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return null;
    }

    protected function getRules(): array
    {
        $rules = parent::getRules();

        foreach ($this->getCachedForms() as $form) {
            $rules = array_merge($rules, $form->getValidationRules());
        }

        return $rules;
    }

    protected function getValidationAttributes(): array
    {
        $attributes = [];

        foreach ($this->getCachedForms() as $form) {
            $attributes = array_merge($attributes, $form->getValidationAttributes());
        }

        return $attributes;
    }

    protected function makeForm(): ComponentContainer
    {
        return ComponentContainer::make($this);
    }
}
