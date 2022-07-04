<?php

namespace Filament\Forms\Components;

/**
 * @deprecated use Repeater with the `relationship()` method instead.
 */
class RelationshipRepeater extends Repeater
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship();
    }
}
