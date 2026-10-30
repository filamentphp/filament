<?php

namespace Filament\Tests\Upgrade\Fixtures\PlainSchemaAliases;

use Closure;
use Filament\Schemas\Schema;

// Pinned forwarding definitions from Filament `19bd1e639e80f7e8d80b1a2c778d97387f87aa07`.
// Registration and canonical implementations come from the installed product.
trait LegacyAliases
{
    protected function cacheForm(string $name, Schema | Closure | null $form): ?Schema
    {
        return $this->cacheSchema($name, $form);
    }

    protected function cacheInfolist(string $name, Schema $schema): ?Schema
    {
        return $this->cacheSchema($name, $schema);
    }

    protected function hasCachedForm(string $name): bool
    {
        return $this->hasCachedSchema($name);
    }

    protected function hasCachedInfolist(string $name): bool
    {
        return $this->hasCachedSchema($name);
    }

    public function getForm(string $name): ?Schema
    {
        return $this->getSchema($name);
    }

    public function getInfolist(string $name): ?Schema
    {
        return $this->getSchema($name);
    }

    public function getCachedForms(): array
    {
        return $this->getCachedSchemas();
    }

    public function getCachedInfolists(): array
    {
        return $this->getCachedSchemas();
    }

    public function isCachingForms(): bool
    {
        return $this->isCachingSchemas();
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

trait EmptyAliases {}

class_alias(getenv('FILAMENT_LEGACY_SCHEMA_ALIASES') ? LegacyAliases::class : EmptyAliases::class, __NAMESPACE__ . '\\RuntimeAliases');
