<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyRepeater extends RelationshipRepeater
{
    public function getRelationship(): MorphMany
    {
        return parent::getRelationship();
    }
}
