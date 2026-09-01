<?php

namespace Filament\Resources\RelationManagers;

class RelationManagerConfiguration
{
    /**
     * @param  class-string<RelationManager>  $relationManager
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $relationManager,
        protected array $properties = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
