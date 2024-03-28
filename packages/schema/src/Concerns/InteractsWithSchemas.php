<?php

namespace Filament\Schema\Concerns;

use Closure;
use Filament\Forms\Components\Contracts\HasFileAttachments;
use Filament\Schema\ComponentContainer;
use Filament\Schema\Components\Component;
use Filament\Support\Concerns\ResolvesDynamicLivewireProperties;
use Filament\Support\Contracts\TranslatableContentDriver;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ReflectionMethod;

trait InteractsWithSchemas
{
    use ResolvesDynamicLivewireProperties;

    /**
     * @var array <string, TemporaryUploadedFile | null>
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
    public array $discoveredSchemaNames = [];

    /**
     * @var array<string, ?ComponentContainer>
     */
    protected array $cachedSchemas = [];

    protected bool $isCachingSchemas = false;

    public function getSchemaComponentFileAttachment(string $key): ?TemporaryUploadedFile
    {
        return data_get($this->componentFileAttachments, $key);
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

        $this->oldSchemaState[$statePath] = data_get($this, $statePath);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        foreach ($this->getCachedSchemas() as $form) {
            $form->callAfterStateUpdated($statePath);
        }
    }

    public function getSchemaComponent(string $key): ?Component
    {
        if (! str($key)->contains('.')) {
            return null;
        }

        $schemaName = (string) str($key)->before('.');

        return $this->getSchema($schemaName)->getComponent($key, isAbsoluteKey: true);
    }

    protected function cacheSchema(string $name, ComponentContainer | Closure | null $schema = null): ?ComponentContainer
    {
        $this->isCachingSchemas = true;

        $schema = value($schema);

        try {
            if ($schema) {
                return $this->cachedSchemas[$name] = $schema;
            }

            // If null was explicitly passed as the schema, unset the cached schema.
            if (func_num_args() === 2) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (! method_exists($this, $name)) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            $methodReflection = new ReflectionMethod($this, $name);
            $parameterReflection = $methodReflection->getParameters()[0] ?? null;

            if (! $parameterReflection) {
                $returnTypeReflection = $methodReflection?->getReturnType();

                if (! $returnTypeReflection) {
                    unset($this->cachedSchemas[$name]);

                    return null;
                }

                $type = $returnTypeReflection->getName();

                if (! is_a($type, ComponentContainer::class, allow_string: true)) {
                    unset($this->cachedSchemas[$name]);

                    return null;
                }

                return $this->cachedSchemas[$name] = ($this->{$name}())->key($name);
            }

            $typeReflection = $parameterReflection?->getType();

            if (! $typeReflection) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            $type = $typeReflection->getName();

            if (! class_exists($type)) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            if (! is_a($type, ComponentContainer::class, allow_string: true)) {
                unset($this->cachedSchemas[$name]);

                return null;
            }

            $schema = $type::make($this)->key($name);

            return $this->cachedSchemas[$name] = $this->{$name}($schema);
        } finally {
            $this->isCachingSchemas = false;
        }
    }

    protected function hasCachedSchema(string $name): bool
    {
        return array_key_exists($name, $this->getCachedSchemas());
    }

    public function getSchema(string $name): ?ComponentContainer
    {
        if ($this->hasCachedSchema($name)) {
            return $this->getCachedSchemas()[$name];
        }

        return $this->cacheSchema($name);
    }

    /**
     * @return array<string, ?ComponentContainer>
     */
    public function getCachedSchemas(): array
    {
        foreach ($this->discoveredSchemaNames as $schemaName) {
            if (array_key_exists($schemaName, $this->cachedSchemas)) {
                continue;
            }

            $this->cacheSchema($schemaName);
        }

        $this->discoveredSchemaNames = [];

        return $this->cachedSchemas;
    }

    #[Renderless]
    public function getSchemaComponentFileAttachmentUrl(string $key): ?string
    {
        $attachment = $this->getSchemaComponentFileAttachment($key);

        if (! $attachment) {
            return null;
        }

        $component = $this->getSchemaComponent($key);

        if (! $component instanceof HasFileAttachments) {
            return null;
        }

        return $component->saveUploadedFileAttachment($attachment);
    }
}
