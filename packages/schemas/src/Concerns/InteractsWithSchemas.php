<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
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

    #[Locked]
    public bool $areSchemaStateUpdateHooksDisabledForTesting = false;

    /**
     * @var array<string, mixed>
     */
    protected array $oldSchemaState = [];

    /**
     * @var array<string>
     */
    #[Locked]
    public array $discoveredSchemaNames = [];

    /**
     * @var array<string, ?Schema>
     */
    protected array $cachedSchemas = [];

    protected bool $isCachingSchemas = false;

    protected ?Schema $currentlyValidatingSchema = null;

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
        } else {
            $schema = $component->getContainer();
            $schemaToPartiallyRender = null;

            while ($schema !== null) {
                if ($schema->shouldPartiallyRender()) {
                    $schemaToPartiallyRender = $schema;
                }

                $schema = $schema->getParentComponent()?->getContainer();
            }

            if ($schemaToPartiallyRender) {
                app(PartialsComponentHook::class)->renderPartial($this, fn (): array => [
                    "schema.{$schemaToPartiallyRender->getKey()}" => $schemaToPartiallyRender->toHtml(...),
                ]);
            }
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
        if (app()->runningUnitTests() && $this->areSchemaStateUpdateHooksDisabledForTesting) {
            return;
        }

        foreach ($this->getCachedSchemas() as $schema) {
            $schema->callAfterStateUpdated($statePath);
        }
    }

    public function disableSchemaStateUpdateHooksForTesting(): void
    {
        if (! app()->runningUnitTests()) {
            return;
        }

        $this->areSchemaStateUpdateHooksDisabledForTesting = true;

        $this->skipRender();
    }

    public function enableSchemaStateUpdateHooksForTesting(): void
    {
        if (! app()->runningUnitTests()) {
            return;
        }

        $this->areSchemaStateUpdateHooksDisabledForTesting = false;

        $this->skipRender();
    }

    /**
     * @param  array<Component>  $skipComponentsChildContainersWhileSearching
     */
    public function getSchemaComponent(string $key, bool $withHidden = false, array $skipComponentsChildContainersWhileSearching = []): Component | Action | ActionGroup | null
    {
        if (! str($key)->contains('.')) {
            return null;
        }

        $schemaName = (string) str($key)->before('.');

        $schema = $this->getSchema($schemaName);

        return $schema?->getComponent($key, withHidden: $withHidden, isAbsoluteKey: true, skipComponentsChildContainersWhileSearching: $skipComponentsChildContainersWhileSearching);
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

                if (! in_array($name, $this->discoveredSchemaNames)) {
                    $this->discoveredSchemaNames[] = $name;
                }

                if (method_exists($this, 'default' . ucfirst($name))) {
                    $this->{'default' . ucfirst($name)}($schema);
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

            if (! in_array($name, $this->discoveredSchemaNames)) {
                $this->discoveredSchemaNames[] = $name;
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
            foreach ($this->discoveredSchemaNames as $schemaName) {
                if (array_key_exists($schemaName, $this->cachedSchemas)) {
                    continue;
                }

                $this->cacheSchema($schemaName);
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
        if ($this->currentlyValidatingSchema) {
            $attributes = $this->currentlyValidatingSchema->mutateStateForValidation($attributes);
        } else {
            foreach ($this->getCachedSchemas() as $schema) {
                $attributes = $schema->mutateStateForValidation($attributes);
            }
        }

        return $attributes;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getRules(): array
    {
        $rules = parent::getRules();

        foreach ($this->getCachedSchemas() as $schema) {
            $rules = [
                ...$rules,
                ...$schema->getValidationRules(),
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

        foreach ($this->getCachedSchemas() as $schema) {
            $attributes = [
                ...$attributes,
                ...$schema->getValidationAttributes(),
            ];
        }

        return $attributes;
    }

    /**
     * @param  array<mixed>  $state
     */
    public function fillFormDataForTesting(array $state = [], ?string $schemaStatePath = null): void
    {
        if (! app()->runningUnitTests()) {
            return;
        }

        foreach (Arr::dot($state) as $statePath => $value) {
            $this->updatingInteractsWithSchemas($statePath);

            data_set($this, $statePath, $value);

            $this->updatedInteractsWithSchemas($statePath);
        }

        foreach (Arr::undot($state) as $statePath => $value) {
            if (! is_array($value)) {
                continue;
            }

            $this->unsetMissingNumericArrayKeys($this->{$statePath}, $value, $statePath, $schemaStatePath);
        }

        $this->skipRender();
    }

    /**
     * @param  array<mixed>  $target
     * @param  array<mixed>  $state
     */
    protected function unsetMissingNumericArrayKeys(array &$target, array $state, string $currentStatePath, ?string $schemaStatePath = null): void
    {
        foreach ($target as $key => $value) {
            $currentStatePath .= ".{$key}";

            if (
                is_numeric($key) &&
                (! array_key_exists($key, $state)) &&
                str($currentStatePath)->startsWith($schemaStatePath)
            ) {
                unset($target[$key]);

                continue;
            }

            if (is_array($value) && is_array($state[$key] ?? null)) {
                $this->unsetMissingNumericArrayKeys($target[$key], $state[$key], $currentStatePath, $schemaStatePath);
            }
        }
    }

    public function currentlyValidatingSchema(?Schema $schema): void
    {
        $this->currentlyValidatingSchema = $schema;
    }

    public function getDefaultTestingSchemaName(): ?string
    {
        return array_key_first($this->getCachedSchemas());
    }
}
