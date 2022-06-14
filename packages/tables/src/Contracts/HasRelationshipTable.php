<?php

namespace Filament\Tables\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

interface HasRelationshipTable
{
    public function getInverseRelationshipName(): string;

    public function getRelationship(): Relation | Builder;
}
