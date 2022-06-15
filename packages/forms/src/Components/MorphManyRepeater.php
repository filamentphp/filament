<?php

namespace Filament\Forms\Components;

/**
 * @deprecated use Repeater with the `relationship()` method instead.
 */
class MorphManyRepeater extends Repeater
{
    public function getRelationshipName(): ?string
    {
        return $this->getName();
    }
}
