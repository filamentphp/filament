<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

/**
 * @deprecated use Repeater with the `relationship()` method instead.
 */
class HasManyRepeater extends Repeater
{
    public function getRelationshipName(): ?string
    {
        return $this->getName();
    }
}
