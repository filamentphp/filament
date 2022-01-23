<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyRepeater extends RelationshipRepeater
{
    public function getRelationship(): HasMany
    {
        return parent::getRelationship();
    }
}
