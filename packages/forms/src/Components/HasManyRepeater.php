<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class HasManyRepeater extends RelationshipRepeater
{
    public function getRelationship(): HasMany | HasOneOrMany
    {
        return parent::getRelationship();
    }
}
