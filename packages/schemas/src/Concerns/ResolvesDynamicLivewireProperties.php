<?php

namespace Filament\Schemas\Concerns;

use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Str;
use Livewire\Exceptions\PropertyNotFoundException;

trait ResolvesDynamicLivewireProperties
{
    /**
     * @param  string  $name
     * @return mixed
     */
    #[Unexposed]
    public function getPropertyValue($name)
    {
        $property = Str::before($name, '.');

        if (property_exists($this, $property)) {
            return parent::getPropertyValue($name);
        }

        try {
            $value = parent::__get($property);
        } catch (PropertyNotFoundException $exception) {
            // Client-supplied property names must not discover schemas or mount actions.
            $value = ($this instanceof HasSchemas ? $this->getCachedSchema($property) : null)
                ?? ($this instanceof HasActions ? ($this->cachedActions[$property] ?? null) : null)
                ?? throw $exception;
        }

        return Str::contains($name, '.') ? data_get($value, Str::after($name, '.')) : $value;
    }

    /**
     * @param  string  $property
     *
     * @throws PropertyNotFoundException
     */
    #[Unexposed]
    public function __get($property): mixed
    {
        try {
            return parent::__get($property);
        } catch (PropertyNotFoundException $exception) {
        }

        if (
            $this instanceof HasSchemas &&
            (! $this->isCachingSchemas()) &&
            $schema = $this->getSchema($property)
        ) {
            return $schema;
        }

        if (
            $this instanceof HasActions &&
            $action = $this->getAction($property, isMounting: false)
        ) {
            return $action;
        }

        throw $exception;
    }
}
