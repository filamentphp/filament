<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use ReflectionMethod;
use ReflectionNamedType;

trait InteractsWithSchemas
{
    use ResolvesDynamicLivewireProperties;
    use WithFileUploads;

    /**
     * @var array <string, TemporaryUploadedFile | array<TemporaryUploadedFile> | null>
     */
    public array $componentFileAttachments = [];

    /**
     * @var array<string, mixed>
     */
    protected array $oldSchemaState = [];

    /**
     * @var array<string>
     */
    #[Locked]
    public array $discoveredSchemaKeys = [];

    /**
     * @var array<string, ?Schema>
     */
    protected array $cachedSchemas = [];

    protected bool $isCachingSchemas = false;

    public function isCachingSchemas(): bool
    {
        return $this->isCachingSchemas;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callSchemaComponentMethod(string $componentKey, string $method, array $arguments = []): mixed
    {
        $component = $this->getSchemaComponent($componentKey);

        if (! $component) {
            return null;
        }

        if (! method_exists($component, $method)) {
            return null;
        }

        $methodReflection = new ReflectionMethod($component, $method);

        if (! $methodReflection->getAttributes(ExposedLivewireMethod::class)) {
            return null;
        }

        if ($methodReflection->getAttributes(Renderless::class)) {
            $this->skipRender();
        }

        return $component->{$method}(...$arguments);
    }

    public function partiallyRenderSchemaComponent(string $componentKey): void
    {
        $this->getSchemaComponent($componentKey)?->partiallyRender();
    }

    /**
     * @return class-string<TranslatableContentDriver> | null
     */
    public function getFilamentTranslatableContentDriver(): ?string
    {
        return null;
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        $driver = $this->getFilamentTranslatableContentDriver();

        if (! $driver) {
            return null;
        }

        return app($driver, ['activeLocale' => $this->getActiveSchemaLocale() ?? app()->getLocale()]);
    }

    public function getActiveSchemaLocale(): ?string
    {
        return null;
    }

    public function getOldSchemaState(string $statePath): mixed
    {
        return data_get($this->oldSchemaState, $statePath);
    }

    public function updatingInteractsWithSchemas(string $statePath): void
    {
        $statePath = (string) str($statePath)->before('.');

        $this->oldSchemaState[$statePath] ??= data_get($this, $statePath);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        foreach ($this->getCachedSchemas() as $form) {
            $form->callAfterStateUpdated($statePath);
        }
    }

    public function getSchemaComponent(string $key, bool $withHidden = false, ?Component $skipComponentChildContainersWhileSearching = null): Component | Action | ActionGroup | null
    {
        if (! str($key)->contains('.')) {
            return null;
        }

        $schemaKey = (string) str($key)->before('.');

        $schema = $this->getSchema($schemaKey);

        return $schema?->getComponent($key, withHidden: $withHidden, isAbsoluteKey: true, skipComponentChildContainersWhileSearching: $skipComponentChildContainersWhileSearching);
    }

    protected function cacheSchema(string $name, Schema | Closure | null $schema = null): ?Schema
    {
        $this->isCachingSchemas = true;

        $schema = value($schema);

        try {
            if ($schema) {
                return $this->cachedSchemas[$name] = $schema->key($name);
            }

            // If null was explicitly passed as the schema, unset the cached schema.
            if (func_num_args() === 2) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (method_exists($this, $name)) {
                $methodName = $name;
            } elseif (method_exists($this, "{$name}Schema")) {
                $methodName = "{$name}Schema";
            } else {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            $methodReflection = new ReflectionMethod($this, $name);
            $parameterReflection = $methodReflection->getParameters()[0] ?? null;

            if (! $parameterReflection) {
                $returnTypeReflection = $methodReflection->getReturnType();

                if (! $returnTypeReflection) {
                    unset($this->cachedSchemas[$name]);

                    return null;
                }

                if (! $returnTypeReflection instanceof ReflectionNamedType) {
                    unset($this->cachedSchemas[$name]);

                    return null;
                }

                $type = $returnTypeReflection->getName();

                if (! is_a($type, Schema::class, allow_string: true)) {
                    unset($this->cachedSchemas[$name]);

                    return null;
                }

                if (! in_array($name, $this->discoveredSchemaKeys)) {
                    $this->discoveredSchemaKeys[] = $name;
                }

                if (method_exists($this, 'default' . ucfirst($name))) {
                    $schema = $this->{'default' . ucfirst($name)}($schema);
                }

                return $this->cachedSchemas[$name] = ($this->{$methodName}())->key($name);
            }

            $typeReflection = $parameterReflection->getType();

            if (! $typeReflection) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (! $typeReflection instanceof ReflectionNamedType) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            $type = $typeReflection->getName();

            if (! class_exists($type)) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (! is_a($type, Schema::class, allow_string: true)) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (! in_array($name, $this->discoveredSchemaKeys)) {
                $this->discoveredSchemaKeys[] = $name;
            }

            $schema = $this->makeSchema();

            if (method_exists($this, 'default' . ucfirst($name))) {
                $schema = $this->{'default' . ucfirst($name)}($schema);
            }

            return $this->cachedSchemas[$name] = $this->{$methodName}($schema)->key($name);
        } finally {
            $this->isCachingSchemas = false;
        }
    }

    protected function makeSchema(): Schema
    {
        return Schema::make($this);
    }

    protected function hasCachedSchema(string $name): bool
    {
        return array_key_exists($name, $this->getCachedSchemas());
    }

    public function getSchema(string $name): ?Schema
    {
        if ($this->hasCachedSchema($name)) {
            return $this->getCachedSchemas()[$name];
        }

        return $this->cacheSchema($name);
    }

    /**
     * @return array<string, ?Schema>
     */
    public function getCachedSchemas(): array
    {
        if (! $this->isCachingSchemas) {
            foreach ($this->discoveredSchemaKeys as $schemaKey) {
                if (array_key_exists($schemaKey, $this->cachedSchemas)) {
                    continue;
                }

                $this->cacheSchema($schemaKey);
            }
        }

        return $this->cachedSchemas;
    }

    /**
     * @param  array<string, array<mixed>> | null  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     * @return array<string, mixed>
     */
    public function validate($rules = null, $messages = [], $attributes = []): array
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            $this->onValidationError($exception);

            $this->dispatch('form-validation-error', livewireId: $this->getId());

            throw $exception;
        }
    }

    /**
     * @param  string  $field
     * @param  array<string, array<mixed>>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     * @param  array<string, string>  $dataOverrides
     * @return array<string, mixed>
     */
    public function validateOnly($field, $rules = null, $messages = [], $attributes = [], $dataOverrides = [])
    {
        try {
            return parent::validateOnly($field, $rules, $messages, $attributes, $dataOverrides);
        } catch (ValidationException $exception) {
            $this->onValidationError($exception);

            $this->dispatch('form-validation-error', livewireId: $this->getId());

            throw $exception;
        }
    }

    protected function onValidationError(ValidationException $exception): void {}

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function prepareForValidation($attributes): array
    {
        foreach ($this->getCachedSchemas() as $form) {
            $attributes = $form->mutateStateForValidation($attributes);
        }

        return $attributes;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getRules(): array
    {
        $rules = parent::getRules();

        foreach ($this->getCachedSchemas() as $form) {
            $rules = [
                ...$rules,
                ...$form->getValidationRules(),
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function getValidationAttributes(): array
    {
        $attributes = parent::getValidationAttributes();

        foreach ($this->getCachedSchemas() as $form) {
            $attributes = [
                ...$attributes,
                ...$form->getValidationAttributes(),
            ];
        }

        return $attributes;
    }

    /**
     * @param  array<mixed>  $state
     */
    public function fillFormDataForTesting(array $state = []): void
    {
        if (! app()->runningUnitTests()) {
            return;
        }

        foreach (Arr::dot($state) as $statePath => $value) {
            $this->updatingInteractsWithSchemas($statePath);

            data_set($this, $statePath, $value);

            $this->updatedInteractsWithSchemas($statePath);
        }

        foreach ($state as $statePath => $value) {
            if (! is_array($value)) {
                continue;
            }

            $this->unsetMissingNumericArrayKeys($this->{$statePath}, $value);
        }
    }

    /**
     * @param  array<mixed>  $target
     * @param  array<mixed>  $state
     */
    protected function unsetMissingNumericArrayKeys(array &$target, array $state): void
    {
        foreach ($target as $key => $value) {
            if (is_numeric($key) && (! array_key_exists($key, $state))) {
                unset($target[$key]);

                continue;
            }

            if (is_array($value) && is_array($state[$key] ?? null)) {
                $this->unsetMissingNumericArrayKeys($target[$key], $state[$key]);
            }
        }
    }
}
