<?php

namespace Filament\Actions;

use Attribute;
use Stringable;

#[Attribute]
class ActionName implements Stringable
{
    public function __construct(protected string $name) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
