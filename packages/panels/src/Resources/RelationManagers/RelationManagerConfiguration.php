<?php

namespace Filament\Resources\RelationManagers;

class RelationManagerConfiguration
{
    /**
     * @param  class-string<RelationManager>  $relationManager
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        readonly public string $relationManager,
        readonly public array $properties = [],
    ) {
    }
}
