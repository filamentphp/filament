<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyRepeater extends RelationshipRepeater
{
    public function getRelationship(): MorphMany | HasOneOrMany
    {
        return parent::getRelationship();
    }
}
