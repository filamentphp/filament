<?php

namespace Filament\Forms\Components;

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
